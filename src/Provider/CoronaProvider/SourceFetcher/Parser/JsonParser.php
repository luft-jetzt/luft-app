<?php declare(strict_types=1);

namespace App\Provider\CoronaProvider\SourceFetcher\Parser;

use App\Pollution\Value\Value;

class JsonParser implements JsonParserInterface
{
    public function parseCoronaIncidence(string $jsonData): Value
    {
        $coronaValue = json_decode($jsonData);

        $value = new Value();
        $value
            ->setValue((float) $coronaValue->cases7_per100_k)
            ->setPollutant('coronaincidence')
            ->setDateTime(new \DateTime($coronaValue->last_update, new \DateTimeZone('Europe/Berlin')))
        ;

        return $value;
    }
}
