<?php declare(strict_types=1);

namespace App\Geocoding\Query;

class GeoQuery extends AbstractGeoQuery
{
    public function query(string $queryString): array
    {
        $this->curl->get(sprintf(self::QUERY_ADRESS, $queryString));

        $features = $this->curl->response->features;

        $result = [];

        foreach ($features as $feature) {
            if (!$feature->properties) {
                continue;
            }

            if (!$feature->properties->country || $feature->properties->country !== 'Deutschland') {
                continue;
            }

            $latitude = $feature->geometry->coordinates[1];
            $longitude = $feature->geometry->coordinates[0];
            $url = $this->router->generate('display', ['latitude' => $latitude, 'longitude' => $longitude]);

            $value = [
                'url' => $url,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'icon' => 'map-marker',
            ];

            if (isset($feature->properties->name)) {
                $value['name'] = $feature->properties->name;

                if (isset($feature->properties->street)) {
                    $value['address'] = $feature->properties->street;
                }
            } elseif (isset($feature->properties->street)) {
                $value['name'] = $feature->properties->street;
            }

            if (isset($feature->properties->city)) {
                $value['city'] = $feature->properties->city;
            }

            if (isset($feature->properties->postcode)) {
                $value['zipCode'] = $feature->properties->postcode;
            }

            if (isset($feature->properties->osm_key) && isset($feature->properties->osm_value)) {
                $osmKey = $feature->properties->osm_key;
                $osmValue = $feature->properties->osm_value;

                if ($osmValue === 'city' || $osmValue === 'suburb') {
                    $value['icon'] = 'university';
                } elseif ($osmValue === 'building' || $osmValue === 'residental') {
                    $value['icon'] = 'road';
                }
            }

            $result[] = [
                'value' => $value
            ];
        }

        return $result;
    }
}
