# Luft.jetzt

Luft.jetzt zeigt die Messwerte von Luftschadstoffen aus der Umgebung eines Nutzers an.

Dazu werden unterschiedliche Datenquellen angezapft, etwa die öffentlich zugänglichen Messwerte des deutschen Umweltbundesamtes sowie vom Luftdaten-Projekt.

## Systemvorraussetzungen

Luft.jetzt ist eine Symfony-8-Anwendung und benötigt PHP 8.5 (siehe `.php-version` und `composer.json`).

Die Daten werden grundsätzlich in einer SQL-Datenbank gespeichert, werden aber zur Beschleunigung des Suchvorganges mit Elasticsearch indiziert; momentan ist mindestens Elasticsearch 6.3 notwendig.

## Installation

1. Clone this repository somewhere on your local machine.
2. Type ````symfony composer install``` to install all dependencies.
3. Get the required docker containers started: ```docker-compose up -d```
4. Create database schema: ```symfony console d:s:c```
5. Unfold a hell of javascripts: ```npm install```
6. Gulp: ```node_modules/gulp/bin/gulp.js```
5. Start Symfony Webserver: ```symfony serve```

