<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher;

use App\Pollution\DataPersister\PersisterInterface;
use App\Pollution\Value\Value;
use App\Provider\Luftdaten\SourceFetcher\Parser\JsonParserInterface;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use App\SourceFetcher\SourceFetcherInterface;
use Curl\Curl;

class SourceFetcher implements SourceFetcherInterface
{
    protected Curl $curl;

    protected JsonParserInterface $parser;

    protected PersisterInterface $persister;

    public function __construct(PersisterInterface $persister, JsonParserInterface $parser)
    {
        $this->persister = $persister;
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
            $fetchResult->incCounter((string) $value->getPollutant()); // @todo
        }

        $this->persister->persistValues($valueList);

        return $fetchResult;
    }

    protected function query(): array
    {
        $this->curl->get('https://api.luftdaten.info/static/v2/data.dust.min.json');

        return $this->curl->response;
    }
}
