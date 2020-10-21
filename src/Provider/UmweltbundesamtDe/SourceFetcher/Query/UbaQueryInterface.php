<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

interface UbaQueryInterface
{
    public function getComponent(): int;

    public function getScope(): array;

    public function getFromDateTime(): \DateTime;

    public function setFromDateTime(\DateTime $fromDateTime): UbaQueryInterface;

    public function getUntilDateTime(): \DateTime;

    public function setUntilDateTime(\DateTime $untilDateTime): UbaQueryInterface;
}
