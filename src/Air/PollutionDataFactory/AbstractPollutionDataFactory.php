<?php declare(strict_types=1);

namespace App\Air\PollutionDataFactory;

use App\Air\DataRetriever\DataRetrieverInterface;
use App\Air\ViewModelFactory\PollutantViewModelFactoryInterface;
use App\Entity\Station;
use Caldera\GeoBasic\Coordinate\CoordinateInterface;

abstract class AbstractPollutionDataFactory implements PollutionDataFactoryInterface
{
    protected CoordinateInterface $coord;

    public function __construct(
        protected PollutantViewModelFactoryInterface $pollutantViewModelFactory,
        protected DataRetrieverInterface $dataRetriever,
    )
    {
    }

    #[\Override]
    public function setCoord(CoordinateInterface $coord): PollutionDataFactoryInterface
    {
        $this->coord = $coord;

        return $this;
    }

    #[\Override]
    public function setStation(Station $station): PollutionDataFactoryInterface
    {
        $this->coord = $station;

        return $this;
    }
}
