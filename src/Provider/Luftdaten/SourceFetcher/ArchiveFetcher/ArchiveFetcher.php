<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\ArchiveFetcher;

use App\Pollution\Value\Value;
use Curl\Curl;
use League\Csv\Reader;

class ArchiveFetcher
{
    const HOST = 'http://archive.luftdaten.info';

    /** @var \DateTimeInterface $dateTime */
    protected $dateTime;

    /** @var Curl $curl */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    protected function generateDirectoryUrl(): string
    {
        return sprintf('%s/%s/', self::HOST, $this->dateTime->format('Y-m-d'));
    }

    protected function generateFileUrl(string $filename): string
    {
        return sprintf('%s/%s/%s', self::HOST, $this->dateTime->format('Y-m-d'), $filename);
    }

    public function setDateTime(\DateTimeInterface $dateTime): ArchiveFetcher
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    protected function fetchStationCsvFiles(): array
    {
        $this->curl->get($this->generateDirectoryUrl());

        $response = $this->curl->response;

        preg_match_All('/(?:href=\\")((.*?)(.csv))(?:\\")/', $response, $csvLinks);

        return $csvLinks[1];
    }

    protected function loadCsvContent(string $csvLink): string
    {
        $this->curl->get($this->generateFileUrl($csvLink));

        return (string) $this->curl->response;
    }

    protected function parseFile(string $csvFileContent): Value
    {
        $csv = Reader::createFromString(utf8_decode($csvFileContent));

        $csv
            ->setDelimiter(';')
            ->setHeaderOffset(0);

        foreach ($csv as $dataLine) {
            var_dump($dataLine);
        }

        die;
    }

    public function fetch(): void
    {
        $csvLinks = $this->fetchStationCsvFiles();

        foreach ($csvLinks as $csvLink) {
            $csvFileContent = $this->loadCsvContent($csvLink);

            $this->parseFile($csvFileContent);
        }
    }

}
