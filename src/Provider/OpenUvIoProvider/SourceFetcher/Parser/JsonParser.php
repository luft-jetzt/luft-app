<?php declare(strict_types=1);

namespace App\Provider\OpenUvIoProvider\SourceFetcher\Parser;

use App\Pollution\Value\Value;

class JsonParser implements JsonParserInterface
{
    #[\Override]
    public function parseUVIndex(string $jsonData): Value
    {
        $jsonData = json_decode($jsonData, null, 512, JSON_THROW_ON_ERROR);

        $value = new Value();
        $value
            ->setValue((float) $jsonData->result->uv)
            ->setPollutant('uvindex')
            ->setDateTime(new \DateTime($jsonData->result->uv_time))
        ;

        return $value;
    }

    #[\Override]
    public function parseUVIndexMax(string $jsonData): Value
    {
        $jsonData = json_decode($jsonData, null, 512, JSON_THROW_ON_ERROR);

        $value = new Value();
        $value
            ->setValue((float) $jsonData->result->uv_max)
            ->setPollutant('uvindex_max')
            ->setDateTime(new \DateTime($jsonData->result->uv_max_time))
        ;

        return $value;
    }
}
