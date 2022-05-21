<?php declare(strict_types=1);

namespace App\Air\ViewModelFactory;

use Caldera\GeoBasic\Coordinate\CoordinateInterface;

interface MeasurementViewModelFactoryInterface
{
    public function setPollutantList(array $pollutantList): self;
    public function getPollutantList(): array;
    public function setCoord(CoordinateInterface $coord): self;
    public function decorate(): self;
}
