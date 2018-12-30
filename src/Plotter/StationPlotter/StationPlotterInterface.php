<?php declare(strict_types=1);

namespace App\Plotter\StationPlotter;

use App\Entity\Station;

interface StationPlotterInterface
{
    public function setFromDateTime(\DateTime $fromDateTime): StationPlotterInterface;
    public function setUntilDateTime(\DateTime $untilDateTime): StationPlotterInterface;
    public function setStation(Station $station): StationPlotterInterface;
    public function setWidth(int $width): StationPlotterInterface;
    public function setHeight(int $height): StationPlotterInterface;
    public function setTitle(string $title): StationPlotterInterface;
    public function plot(): void;
}
