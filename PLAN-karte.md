# PLAN: Deutschland-Übersichtskarte für luft.jetzt (`/karte`)

Stand: 2026-07-30. Grundlage: Code-Recherche (HEAD 35b110e) + Referenz waldbrand.jetzt `/karte`.

## 1. Ziel & Nutzererlebnis

Eine eigenständige Kartenseite `luft.jetzt/karte` im Atmosphäre-Design: ganz Deutschland im
Blick, Marker je Messstation in der Farbe ihrer aktuellen Belastung, per Klick Werte und
Absprung zur Stationsseite.

- **Start-Ausschnitt**: Deutschland-Zentrum `[51.3, 10.4]`, Zoom 6 (wie waldbrand).
- **Marker**: `L.circleMarker` in den Atmosphäre-AQI-Farben (`--aqi-1…5`), weißer Rand,
  grau/transparent für „keine aktuellen Daten". Farblogik identisch zur bestehenden
  Detailkarte (`assets/js/atmosphaere.js:5`).
- **Popup**: Stationsname, Wert + Einheit des gewählten Schadstoffs (bzw. beim Gesamtindex
  der bestimmende Schadstoff), „vor X Min"-Zeitstempel, Link zur Stationsseite.
- **Schadstoff-Umschalter** (Glas-Chips über der Karte): Gesamtindex (Default) · PM10 ·
  PM2.5 · NO₂ · O₃ · SO₂ · CO · UV-Index · Temperatur. Umschalten färbt die Marker um und
  lädt den jeweiligen Layer.
- **Datenquellen-Umschalter**: „Amtlich" (UBA + BfS, ~570 Stationen) vs. zusätzlich
  „Sensor.Community" (~12.160 Stationen). Default: nur amtlich; SC-Marker werden erst ab
  Zoom ≥ 9 und nur für den sichtbaren Ausschnitt nachgeladen (s. §4).
- **Legende**: 5 Stufen (Sehr gut → Sehr schlecht, Labels aus `at_verdict`) + „keine Daten".
- **Geolocation-Button**: „Zu mir" — zentriert die Karte auf den Standort (nur Client-seitig,
  keine Koordinaten an den Server; Muster `initGeolocation()`).
- **Mobile**: Karte ~`calc(100dvh - Topbar)`, `scrollWheelZoom` hier aktiv (dedizierte Seite),
  Bedienelemente als überlagerte Chips.

Nicht in v1: Zeitreise/Historie, Heatmap-Interpolation, Suchfeld auf der Karte (v2-Kandidat:
bestehendes Autocomplete wiederverwenden).

## 2. Neuer API-Endpunkt

`GET /api/map/stations.geojson?pollutant=all|pm10|pm25|no2|o3|so2|co|uvindex|temperature&scope=official|all[&bbox=w,s,e,n]`

- **Response**: GeoJSON `FeatureCollection`; Feature-`properties` kompakt:
  `{ c: stationCode, n: name, u: url, l: skyLevel 0–5, v: value, un: unit, p: pollutantShort, t: unixTs, pr: provider }`.
  Level/Label-Quelle ist der Server (ein System of Record, wie waldbrand `FireDangerLevel`);
  der Client mappt nur `l` → `--aqi-N`.
- **Daten-Query** (das N+1 über `PollutionDataFactory` ist für 12,7k Stationen unbrauchbar —
  `AbstractController::createViewModelListForStationList()` macht 1 Query/Station):
  Ein einziges SQL auf der Materialized View `current_data`:
  `SELECT DISTINCT ON (station_code, pollutant) station_code, title, latitude, longitude,
  pollutant, value, date_time, provider FROM current_data [WHERE-Filter] ORDER BY
  station_code, pollutant, date_time DESC` — danach in PHP `LevelCalculator` je Schadstoff,
  bei `pollutant=all` Maximum je Station (Logik aus `LevelColorHandler::calculateMaxLevel`),
  Mapping 0–6 → Sky 0–5 über die Tabelle aus `AtmosphaereTwigExtension::SKY_LEVEL`.
- **Schlüssel ist `station_code`**, NICHT `station_id` — die MV mappt `id` doppelt
  (dokumentierter Bug in `DataRepository::findCurrentDataForCoord`, RSM Z. 71/77).
- **Staleness-Filter**: nur Werte jünger als z. B. 3 h (UV/Temperatur ggf. eigene Fenster).
  ⚠️ Vorher auf Prod die MV-Definition prüfen (`\d+ current_data` via psql) — die DDL liegt
  nicht im Repo; unbekannt, ob die View selbst schon ein Zeitfenster filtert.
- **`scope=official`**: `provider = 'uba_de' OR station_code ~ '^(BFS|UBA|GAAHI|DWD|TROPOS|IFA|BAUA)'`
  (BfS-Stationen haben KEINEN provider-Wert; Präfixe aus `provider-bfs Namer.php`).
  `scope=all` ergänzt `ld` (Sensor.Community) — nur in Kombination mit `bbox` erlaubt.
- **Caching**: Symfony-Cache (Redis-Pool) 5 min je Parameterkombination (official-Varianten
  vorwärmbar), plus HTTP `Cache-Control: public, max-age=300` (erster Cache-Header in der
  API überhaupt — CORS-Subscriber greift bereits für `/api/*`). bbox-Requests: nur
  HTTP-Cache, kein Redis (Kombinatorik).
- **Payload-Budget**: amtlich ≈ 570 Features → wenige 100 KB unkomprimiert, gzip < 50 KB.
  SC via bbox-Nachladen bleibt klein. Referenz-Messwerte: `/api/station` (alles, fette
  Struktur) = 1,7 MB/1,5 s → mit schlanken Properties + MV-Query + Cache deutlich darunter.

## 3. Backend-Umsetzung

1. `src/Repository/DataRepository.php`: neue Methode `findCurrentDataForMap(?string $scope, ?array $bbox): array`
   (DISTINCT-ON-Query oben, Native Query mit ResultSetMapping wie die bestehenden Methoden).
2. Neuer `src/Air/Map/MapDataFactory.php` (o. ä.): Rohzeilen → Features; nutzt
   `LevelCalculator` + `PollutantInterface`-Registry (Einheiten/Kurznamen aus den
   vorhandenen Pollutant-Klassen, UVIndexMax→UVIndex-Mapping wie in `PollutionDataFactory`).
3. Neuer `src/Controller/Api/MapApiController.php`: Parameter-Validierung (pollutant-Whitelist,
   bbox-Parsing/Klemmen auf Deutschland-Umriss, scope), Cache, JsonResponse.
4. Route in `config/routing/2_api.yaml` (`/api/map/stations.geojson`, keine Kollision).
5. Tests: Unit für Factory (Level-/Sky-Mapping, Max-Bildung, UVIndexMax-Zusammenführung),
   Integration für den Endpunkt (Fixture-Stationen+Daten, MV-Refresh im Test oder Query
   gegen `data`-Tabelle in Testumgebung — prüfen, ob die Test-DB die MV hat!).

## 4. Frontend-Umsetzung

1. **Route/Seite**: ⚠️ `/karte` DARF NICHT als Attribut-Route angelegt werden — die
   YAML-Directory-Routen laden zuerst und `6_city.yaml` (`/{citySlug}`, `^[A-Za-z-]+$`)
   verschluckt `/karte` (live verifiziert: 404 „City karte"). Lösung: Route `map` in
   `config/routing/4_frontend.yaml` ergänzen (lädt vor `6_city`), Controller
   `src/Controller/MapController.php`, Template `templates/Atmosphaere/map.html.twig`
   (erbt `Atmosphaere/base.html.twig`; `sky_class` = `at-idle`).
2. **JS**: neues `initOverviewMap()` in `assets/js/atmosphaere.js` (Selektor
   `[data-at-map-overview]`, dynamischer `import('leaflet')` wie `initMaps()`):
   - `L.map({ preferCanvas: true })` — Canvas-Renderer, damit auch 12k Kreise flüssig sind.
   - GeoJSON-Layer einmal anlegen, bei Schadstoff-/Scope-Wechsel `clearLayers()+addData()`
     (waldbrand-Muster `map_controller.js`).
   - **Stufenmodell statt Cluster-Plugin**: Zoom < 9 → nur `scope=official` (statischer,
     gecachter Layer). Ab Zoom ≥ 9 → zusätzlich `scope=all&bbox=<aktueller Ausschnitt>`
     nachladen (debounced auf `moveend`), Marker außerhalb verwerfen. Kein
     `leaflet.markercluster` nötig; falls sich das Stufenmodell in der Praxis als unruhig
     erweist, ist das Plugin der Fallback (Entscheidung nach M4-Test mit echten Daten).
   - Popup aus Feature-Properties (kein Zweitrequest), Link auf `u`.
   - Matomo-Event beim Umschalten: `trackEvent('Karte', 'Schadstoff'|'Quelle', wert)`.
3. **SCSS** (`assets/scss/atmosphaere.scss`): `.at-map--full { height: calc(100dvh - …) }`,
   Chip-Leiste + Legende (Tokens `--aqi-*` wiederverwenden), Marker-Grau für Level 0.
4. Encore-Build (Node 20 via nvm, `npm run build`).

## 5. SEO, Navigation, Rechtliches

- Title `Luftqualitäts-Karte Deutschland — luft.jetzt`, Description („Alle Luftmessstationen
  auf einer Karte: Feinstaub, Stickstoffdioxid, Ozon und UV-Index in Echtzeit."), Canonical
  kommt automatisch (KernelEventSubscriber, Query wird verworfen — gut, da Kartenzustand in
  Query-Parametern landen kann), Sitemap-Eintrag in `SitemapEventSubscriber::registerStaticUrls`.
- Startseiten-/Footer-Link „Karte" (Atmosphäre-Start + Legacy-Footer).
- robots: nichts nötig (`/api/` ist bereits disallowed — der GeoJSON-Endpunkt bleibt crawlfrei).
- Datenschutz: keine Änderung nötig — Geolocation bleibt clientseitig, OSM-Kacheln sind
  bereits im Datenschutztext (OpenStreetMap-Abschnitt vorhanden).

## 6. Betrieb & Risiken

| Risiko | Umgang |
|---|---|
| MV `current_data` ist nur auf Prod definiert, DDL nicht im Repo | Vor M1: DDL per `pg_dump --schema-only` sichern und als Migration/Doku ins Repo bringen |
| 12k SC-Marker | Stufenmodell (Zoom/bbox) + Canvas-Renderer; Fallback markercluster |
| API-Kosten cold | Redis-TTL 5 min + optional Vorwärmen der official-Varianten nach `luft:refresh` |
| `/karte` von City-Route verschluckt | Route ausschließlich via `4_frontend.yaml` (Ladereihenfolge) |
| MV-Zeilen ohne `station_id`-Verlass | konsequent `station_code` als Schlüssel |
| Test-DB ohne MV | Integrationstest legt MV an oder Endpunkt-Query abstrahieren |

## 7. Milestones

- **M1** API: MV-DDL sichern → Repository-Query → MapDataFactory → Endpunkt + Cache + Tests.
- **M2** Kartenseite: Route (4_frontend!), Controller, Template, `initOverviewMap()` mit
  amtlichen Stationen, Legende, Geolocation-Button.
- **M3** Schadstoff-Umschalter + Popups + Matomo-Events.
- **M4** Sensor.Community-Layer (Zoom-/bbox-Nachladen), Performance-Test mit echten Daten;
  Entscheidung Stufenmodell vs. markercluster.
- **M5** SEO (Title/Description/Sitemap), Navigation-Links, mobiler Feinschliff.
- **M6** Deploy: Encore-Build (Node 20), rsync + cache:clear, Live-Verifikation
  (Endpunkt-Latenz, Karte auf Mobile, Matomo-Hits).

## 8. Offene Produktfragen (vor M4 klärbar, blockieren M1–M3 nicht)

1. Sollen Sensor.Community-Stationen per Default sichtbar sein (ab Zoom 9) oder nur nach
   aktivem Opt-in über den Quellen-Chip?
2. UV-Index & Temperatur als vollwertige Layer oder hinter „mehr…"? (BfS-UV hat nur 39
   Stationen — auf Zoom 6 gut darstellbar.)
3. Soll die Startseite prominent auf die Karte verweisen (Chip unter dem Suchfeld)?
