<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\ArchiveFetcher;

use App\Pollution\Pollutant\PollutantInterface;
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

    protected function checkHeaderColumns(array $headerColumns): bool
    {
        $requiredColumns = [
            'timestamp',
            'location',
            'P1',
            'P2',
        ];

        $result = true;

        foreach ($requiredColumns as $requiredColumn) {
            if (!in_array($requiredColumn, $headerColumns)) {
                $result = false;

                break;
            }
        }

        return $result;
    }

    protected function checkSensorName(string $csvFilename): bool
    {
        $acceptedSensorNames = [
            'pms5003_sensor',
            'pms7003_sensor',
            'sds011_sensor',
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

    protected function parseFile(string $csvFileContent): array
    {
        $valueList = [];

        $csv = Reader::createFromString(utf8_decode($csvFileContent));

        $csv
            ->setDelimiter(';')
            ->setHeaderOffset(0);

        if (!$this->checkHeaderColumns($csv->getHeader())) {
            return [];
        }

        foreach ($csv as $dataLine) {
            $dateTime = new \DateTime($dataLine['timestamp']);
            $stationCode = sprintf('LFTDTN%d', $dataLine['location']);

            $pm10Value = new Value();
            $pm10Value->setPollutant(PollutantInterface::POLLUTANT_PM10)
                ->setDateTime($dateTime)
                ->setStation($stationCode)
                ->setValue((float) $dataLine['P1']);

            $pm25Value = new Value();
            $pm25Value->setPollutant(PollutantInterface::POLLUTANT_PM25)
                ->setDateTime($dateTime)
                ->setStation($stationCode)
                ->setValue((float) $dataLine['P2']);

            $valueList[] = $pm10Value;
            $valueList[] = $pm25Value;
        }

        return $valueList;
    }

    public function fetch(): array
    {
        $valueList = [];

        $csvLinks = $this->fetchStationCsvFiles();

        foreach ($csvLinks as $csvLink) {
            if (!$this->checkSensorName($csvLink)) {
                return [];
            }

            $csvFileContent = $this->loadCsvContent($csvLink);

            $valueList = array_merge($this->parseFile($csvFileContent), $valueList);
        }

        return $valueList;
    }

}
