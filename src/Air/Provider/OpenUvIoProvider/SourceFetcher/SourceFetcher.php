<?php declare(strict_types=1);

namespace App\Air\Provider\OpenUvIoProvider\SourceFetcher;

use App\Air\SourceFetcher\FetchProcess;
use App\Air\SourceFetcher\FetchResult;
use App\Air\SourceFetcher\SourceFetcherInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use GuzzleHttp\Client;

class SourceFetcher implements SourceFetcherInterface
{
    protected static $response;

    public function __construct(protected string $openUvIoKey)
    {

    }

    #[\Override]
    public function fetch(FetchProcess $fetchProcess): FetchResult
    {
        $fetchResult = new FetchResult();

        if (array_key_exists('uvindex_max', $fetchProcess->getMeasurementList()) && $fetchProcess->getCoord()) {
            $this->queryUVMaxIndex($fetchProcess->getCoord());

            $fetchResult->incCounter('uvindex_max');
        }

        return $fetchResult;
    }

    public function queryUVMaxIndex(CoordInterface $coord): string
    {
        return $this->query($coord);
    }

    protected function query(CoordInterface $coord): string
    {
        if (static::$response) {
            return static::$response;
        }

        $url = sprintf('https://api.openuv.io/api/v1/uv?lat=%f&lng=%f', $coord->getLatitude(), $coord->getLongitude());

        $response = (new Client())->get($url, [
            'headers' => [
                'x-access-token' => $this->openUvIoKey,
            ]
        ]);

        return $response->getBody()->getContents();
    }
}
