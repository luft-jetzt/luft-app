<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher;

use App\Pollution\Value\Value;
use App\Producer\Value\ValueProducerInterface;
use App\Provider\Luftdaten\SourceFetcher\Parser\JsonParserInterface;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use App\SourceFetcher\SourceFetcherInterface;
use Curl\Curl;

class SourceFetcher implements SourceFetcherInterface
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

    public function fetch(FetchProcess $fetchProcess): FetchResult
    {
        $response = $this->query();

        $valueList = $this->parser->parse($response);

        $fetchResult = new FetchResult();

        /** @var Value $value */
        foreach ($valueList as $value) {
            $this->valueProducer->publish($value);
            $fetchResult->incCounter((string) $value->getPollutant()); // @todo
        }

        return $fetchResult;
    }

    protected function query(): array
    {
        $this->curl->get('https://api.luftdaten.info/static/v2/data.dust.min.json');

        return $this->curl->response;
    }
}
