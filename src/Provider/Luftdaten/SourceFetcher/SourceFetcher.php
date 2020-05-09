<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher;

use App\Producer\Value\ValueProducerInterface;
use App\Provider\Luftdaten\SourceFetcher\Parser\JsonParserInterface;
use Curl\Curl;

class SourceFetcher
{
    protected Curl $curl;

    protected JsonParserInterface $parser;

    protected ValueProducerInterface $valueProducer;

    public function __construct(ValueProducerInterface $valueProducer, JsonParserInterface $parser)
    {
        $this->valueProducer = $valueProducer;
        $this->parser = $parser;

        $this->curl = new Curl();
    }

    public function fetch(array $measurements): void
    {
        $response = $this->query();

        $valueList = $this->parser->parse($response);

        foreach ($valueList as $value) {
            $this->valueProducer->publish($value);
        }
    }

    protected function query(): array
    {
        $this->curl->get('https://api.luftdaten.info/static/v2/data.dust.min.json');

        return $this->curl->response;
    }
}
