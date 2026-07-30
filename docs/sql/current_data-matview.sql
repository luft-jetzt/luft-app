-- Materialized View "current_data"
--
-- Definition am 2026-07-30 aus der Produktions-Datenbank gesichert (~254.612 Zeilen).
-- Die MV liegt nicht in den Doctrine-Migrationen; sie wird von der App nur gelesen
-- (DataRepository) und per "REFRESH MATERIALIZED VIEW" über den Befehl luft:refresh
-- aktualisiert (DataRepository::refreshMaterializedView()).
--
-- Die View filtert selbst bereits auf aktuelle Werte: nur Daten der letzten fünf
-- Stunden — mit Ausnahme von Pollutant 7 (CO2, Langzeitmessung), dort 14 Tage.

CREATE MATERIALIZED VIEW current_data AS
SELECT d.id,
       d.value,
       d.pollutant,
       d.date_time,
       s.id AS station_id,
       s.title,
       s.latitude,
       s.longitude,
       s.station_code,
       s.station_type,
       s.provider,
       s.coord
FROM data d
         JOIN station s ON s.id = d.station_id
WHERE ((d.date_time > now() - interval '05:00:00' AND d.date_time < now() + interval '00:30:00')
    OR (d.pollutant = 7 AND d.date_time > now() - interval '14 days'))
ORDER BY d.pollutant, s.provider, d.date_time DESC;
