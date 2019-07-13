<?php declare(strict_types=1);

namespace App\Twitter;

interface TwitterInterface
{
    public function tweet(): void;

    public function getValidScheduleList(): array;

    public function getDryRun(): bool;
    public function setDryRun(bool $dryRun): TwitterInterface;
}
