<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider\SourceFetcher;

use Curl\Curl;

class SourceFetcher
{
    /** @var Curl $curl */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function query(): string
    {
        $this->curl->get('http://hqcasanova.com/co2/?callback=process');

        return $this->curl->response;
    }
}
