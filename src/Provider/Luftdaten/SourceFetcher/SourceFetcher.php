<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher;

use Curl\Curl;

class SourceFetcher
{
    /** @var Curl $curl */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function query(): array
    {
        $this->curl->get('https://api.luftdaten.info/static/v2/data.dust.min.json');

        return $this->curl->response;
    }
}
