<?php declare(strict_types=1);

namespace App\Pollution\BoxDecorator;

use Caldera\GeoBasic\Coord\CoordInterface;

interface BoxDecoratorInterface
{
    public function setPollutantList(array $pollutantList): BoxDecoratorInterface;
    public function getPollutantList(): array;
    public function setCoord(CoordInterface $coord): BoxDecoratorInterface;
    public function decorate(): BoxDecoratorInterface;
}
