<?php declare(strict_types=1);

namespace App\Pollution\ViewModelFactory;

use Caldera\GeoBasic\Coord\CoordInterface;

interface ViewModelFactoryInterface
{
    public function setPollutantList(array $pollutantList): self;
    public function getPollutantList(): array;
    public function setCoord(CoordInterface $coord): self;
    public function decorate(): self;
}
