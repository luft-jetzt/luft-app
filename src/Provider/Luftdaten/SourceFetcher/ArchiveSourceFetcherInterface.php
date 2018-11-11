<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher;

interface ArchiveSourceFetcherInterface
{
    public function setDateTime(\DateTimeInterface $dateTime): ArchiveSourceFetcherInterface;
    public function fetchStationCsvFiles(): ArchiveSourceFetcher;
    public function getCsvLinkList(): array;
    public function loadCsvContent(string $csvLink): string;
}
