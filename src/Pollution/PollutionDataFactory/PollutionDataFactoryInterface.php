<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Station;
use App\Pollution\Box\Box;
use App\Pollution\BoxDecorator\BoxDecoratorInterface;
use App\Pollution\DataList\DataList;
use App\Pollution\DataRetriever\DataRetrieverInterface;
use App\Pollution\StationFinder\StationFinderInterface;
use Caldera\GeoBasic\Coord\CoordInterface;

interface PollutionDataFactoryInterface
{
    public function setCoord(CoordInterface $coord): PollutionDataFactoryInterface;

    public function setStation(Station $station): PollutionDataFactoryInterface;
}
