# Luft.jetzt

Luft.jetzt zeigt die Messwerte von Luftschadstoffen aus der Umgebung eines Nutzers an.

Dazu werden unterschiedliche Datenquellen angezapft, etwa die öffentlich zugänglichen Messwerte des deutschen Umweltbundesamtes sowie vom Luftdaten-Projekt.

## Systemvorraussetzungen

Luft.jetzt ist eine Symfony-4-Anwendung und benötigt den üblichen LAMP-Stack mit PHP 7.1.

Die Daten werden grundsätzlich in einer SQL-Datenbank gespeichert, werden aber zur Beschleunigung des Suchvorganges mit Elasticsearch indiziert; momentan ist mindestens Elasticsearch 6.3 notwendig.

## Installation

1. Repository klonen und in eine Umgebung mit PHP und MySQL und so kopieren.
2. Backend-Abhängigkeiten installieren mit `composer install`
3. Frontend-Abhängigkeiten installieren mit `npm install`
4. einige Frontend-Assets kopieren: `bin/console assets:install --symlink public`
5. Frontend-Assets bauen: `gulp`
6. Datenbank-Schema erstellen: `bin/console doctrine:schema:create`
7. Stationsdaten laden:
    1. Umweltbundesamt: `bin/console luft:station uba_de`
    2. Luftdaten: `bin/console luft:station ld`
8. Schadstoffwerte laden: `bin/console luft:fetch --co --no2 --so2 --o3 --pm10`
