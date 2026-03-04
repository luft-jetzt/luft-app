# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Luft.jetzt is a Symfony 8.0 (PHP 8.5) web application that aggregates and displays air quality / pollution data from multiple sources (German Umweltbundesamt, Luftdaten, OpenWeatherMap). It uses PostgreSQL with PostGIS for geospatial queries, Elasticsearch for search, and Redis for caching.

## Common Commands

### Development Setup
```bash
docker-compose up -d              # Start PostgreSQL/PostGIS, Redis, Elasticsearch
composer install                  # Install PHP dependencies (runs cache:clear + assets:install)
npm install && npm run dev        # Install JS deps and build frontend assets
php bin/console doctrine:migrations:migrate  # Run database migrations
symfony serve                     # Start local Symfony web server
```

### Testing
```bash
vendor/bin/phpunit --testsuite="Project Test Suite"   # Unit tests (excludes Controller/)
vendor/bin/phpunit --testsuite="Integration"           # Integration tests (Controller/)
vendor/bin/phpunit tests/Air/SomeTest.php              # Run a single test file
vendor/bin/phpunit --filter testMethodName              # Run a single test method
```

### Static Analysis
```bash
vendor/bin/phpstan analyse --no-progress   # PHPStan level 5 (config: phpstan.neon)
```

### Frontend Build
```bash
npm run dev-server    # Webpack dev server with HMR
npm run watch         # Watch mode
npm run build         # Production build
```

## Architecture

### Domain Layer (`src/Air/`)

The core domain logic lives in `src/Air/` with these key subsystems:

- **Pollutant/** — Each pollutant (PM10, PM25, NO2, O3, SO2, CO, CO2, Temperature, UVIndex) implements `PollutantInterface` and is auto-registered via `PollutantCompilerPass`
- **Provider/** — Data source providers implement `ProviderInterface`, auto-registered via `ProviderCompilerPass`
- **AirQuality/** — Pollution levels (`PollutionLevelInterface`) and level colors (`LevelColorsInterface`) are auto-registered via their respective compiler passes
- **DataRetriever/** — `ChainedDataRetriever` chains multiple `DataRetrieverInterface` implementations (PostGIS, Adhoc)
- **DataPersister/** — `PersisterInterface` with `PostgisPersister` for writing data
- **PollutionDataFactory/** — Factory pattern creating pollution data ViewModels
- **Analysis/** — Specialized analyses (Fireworks/New Year PM10, EU limit thresholds, Corona-era air quality)
- **Geocoding/** — Nominatim-based location lookup with Redis caching

### Auto-Registration Pattern

The `Kernel.php` registers four compiler passes that auto-collect tagged services. To add a new pollutant, provider, pollution level, or level color scheme, implement the corresponding interface — it will be tagged and collected automatically.

### Entities (`src/Entity/`)

Four main Doctrine entities, all PostGIS-enabled via `jsor/doctrine-postgis`:
- **Station** — Measurement stations with geographic coordinates (unique on `stationCode`)
- **City** — Cities with slug for URL routing
- **Data** — Individual pollution data points tied to stations
- **Network** — Station network metadata

Custom DBAL types in `src/DBAL/Types/`: `StationType`, `AreaType`, UTC datetime types.

### API (`src/Controller/Api/`)

REST API under `/api` with Swagger docs at `/api/doc` and OpenAPI JSON at `/api/doc.json`. Routes defined in XML files under `config/routing/` (numbered for load order: `1_static.xml` through `6_city.xml`).

**Note:** API mutation endpoints (PUT/POST) currently have no authentication. See Security section below.

### Frontend

Webpack Encore with two JS entry points (`app.js`, `datatables.js`) and SCSS. Uses Bootstrap 5, Leaflet for maps, Chart.js, Typeahead/Bloodhound for search, and Handlebars templates.

## Infrastructure

- **Database**: PostgreSQL 15 + PostGIS 3.3 (port 25432 via Docker, DB: `gis`, user: `docker`)
- **Search**: Elasticsearch 7.17.2 (port 9200)
- **Cache**: Redis (port 6379)
- **Locale**: German (`de`), host: `luft.jetzt`

## CI/CD

GitHub Actions (`.github/workflows/`): PHPUnit and PHPStan run on push/PR to `main`. Both use PHP 8.5 with `--no-scripts` for composer install.

## Known Security Considerations

The following items are known and should be addressed when hardening for production:

- **No API authentication**: PUT/POST endpoints (`/api/station`, `/api/city`, `/api/value`) are unauthenticated
- **CSRF protection disabled**: `csrf_protection` is commented out in `config/packages/framework.yaml`
- **No security headers**: CSP, HSTS, X-Frame-Options are not configured
- **No rate limiting** on API endpoints
- **Docker services** (Redis, Elasticsearch) run without authentication and with exposed ports
- **EntityMerger** uses reflection to merge all non-`@Ignore` properties — ensure sensitive fields are properly annotated
- **Twig `|raw` usage**: `unitHtml`, `shortNameHtml`, and `exceedanceJson` use `|raw` — these values must never contain user-controlled input
