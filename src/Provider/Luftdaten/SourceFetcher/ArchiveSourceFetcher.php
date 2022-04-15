<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher;

use Curl\Curl;

class ArchiveSourceFetcher implements ArchiveSourceFetcherInterface
{
    const HOST = 'http://archive.luftdaten.info';

    protected Curl $curl;
    protected array $csvLinkList;
    protected \DateTimeInterface $dateTime;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function setDateTime(\DateTimeInterface $dateTime): ArchiveSourceFetcherInterface
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    protected function generateDirectoryUrl(): string
    {
        return sprintf('%s/%s/', self::HOST, $this->dateTime->format('Y-m-d'));
    }

    protected function generateFileUrl(string $filename): string
    {
        return sprintf('%s/%s/%s', self::HOST, $this->dateTime->format('Y-m-d'), $filename);
    }

    public function fetchStationCsvFiles(): ArchiveSourceFetcher
    {
        $this->curl->get($this->generateDirectoryUrl());

        $response = $this->curl->response;

        preg_match_All('/(?:href=\\")((.*?)(.csv))(?:\\")/', $response, $csvLinks);

        $this->csvLinkList = $csvLinks[1];

        return $this;
    }

    public function getCsvLinkList(): array
    {
        return $this->csvLinkList;
    }

    public function loadCsvContent(string $csvLink): string
    {
        $this->curl->get($this->generateFileUrl($csvLink));

        return (string) $this->curl->response;
    }
}
