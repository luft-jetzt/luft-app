<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\ArchiveFetcher;

interface ArchiveFetcherInterface
{
    public function setCsvLinkList(array $csvLinkList): ArchiveFetcherInterface;
    public function fetch(callable $callback): array;
}
