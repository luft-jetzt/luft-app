<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\ArchiveFetcher;

use App\Provider\Luftdaten\SourceFetcher\ArchiveSourceFetcherInterface;
use App\Provider\Luftdaten\SourceFetcher\Parser\CsvParserInterface;
use Curl\Curl;

class ArchiveFetcher implements ArchiveFetcherInterface
{
    protected Curl $curl;
    protected array $csvLinkList = [];
    protected CsvParserInterface $csvParser;
    protected ArchiveSourceFetcherInterface $archiveSourceFetcher;

    public function __construct(CsvParserInterface $csvParser, ArchiveSourceFetcherInterface $archiveSourceFetcher)
    {
        $this->csvParser = $csvParser;
        $this->archiveSourceFetcher = $archiveSourceFetcher;
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

    public function setCsvLinkList(array $csvLinkList): ArchiveFetcherInterface
    {
        $this->csvLinkList = $csvLinkList;

        return $this;
    }

    public function fetch(callable $callback): array
    {
        $valueList = [];

        foreach ($this->csvLinkList as $csvLink) {
            $callback();

            if (!$this->checkSensorName($csvLink)) {
                continue;
            }

            $csvFileContent = $this->archiveSourceFetcher->loadCsvContent($csvLink);

            $valueList = array_merge($this->csvParser->parse($csvFileContent), $valueList);
        }

        return $valueList;
    }
}
