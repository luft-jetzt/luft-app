<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\ArchiveFetcher;

use App\Provider\Luftdaten\SourceFetcher\Parser\CsvParserInterface;
use Curl\Curl;

class ArchiveFetcher
{
    const HOST = 'http://archive.luftdaten.info';

    /** @var \DateTimeInterface $dateTime */
    protected $dateTime;

    /** @var Curl $curl */
    protected $curl;

    /** @var array $csvLinkList */
    protected $csvLinkList = [];

    /** @var CsvParserInterface $csvParser */
    protected $csvParser;

    public function __construct(CsvParserInterface $csvParser)
    {
        $this->curl = new Curl();
        $this->csvParser = $csvParser;
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

    public function fetchStationCsvFiles(): ArchiveFetcher
    {
        $this->curl->get($this->generateDirectoryUrl());

        $response = $this->curl->response;

        preg_match_All('/(?:href=\\")((.*?)(.csv))(?:\\")/', $response, $csvLinks);

        $this->csvLinkList = $csvLinks[1];

        return $this;
    }

    protected function loadCsvContent(string $csvLink): string
    {
        $this->curl->get($this->generateFileUrl($csvLink));

        return (string) $this->curl->response;
    }

    protected function checkSensorName(string $csvFilename): bool
    {
        $acceptedSensorNames = [
            'pms5003_sensor',
            'pms7003_sensor',
            'sds011_sensor',
            'sds018_sensor',
            'sds021_sensor',
            'ppd42ns_sensor',
            'hpm_sensor',
        ];
        
        $result = false;

        foreach ($acceptedSensorNames as $acceptedSensorName) {
            if (false !== strpos($csvFilename, $acceptedSensorName)) {
                $result = true;

                break;
            }
        }

        return $result;
    }

    public function getCsvLinkList(): array
    {
        return $this->csvLinkList;
    }

    public function fetch(callable $callback): array
    {
        $valueList = [];

        foreach ($this->csvLinkList as $csvLink) {
            $callback();

            if (!$this->checkSensorName($csvLink)) {
                continue;
            }

            $csvFileContent = $this->loadCsvContent($csvLink);

            $valueList = array_merge($this->csvParser->parse($csvFileContent), $valueList);
        }

        return $valueList;
    }
}
