<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Air\Measurement\MeasurementInterface;
use App\Air\MeasurementList\MeasurementListInterface;

class MeasurementTwigExtension extends \Twig_Extension
{
    /** @var MeasurementListInterface $measurementList */
    protected $measurementList;

    public function __construct(MeasurementListInterface $measurementList)
    {
        $this->measurementList = $measurementList;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('pollutant_list', [$this, 'measurementList'], ['is_safe' => ['raw']]),
            new \Twig_SimpleFunction('measurement_list', [$this, 'measurementList'], ['is_safe' => ['raw']]),
            new \Twig_SimpleFunction('pollutant_by_id', [$this, 'measurementById'], ['is_safe' => ['raw']]),
            new \Twig_SimpleFunction('measurement_by_id', [$this, 'measurementById'], ['is_safe' => ['raw']]),
        ];
    }

    public function measurementList(): array
    {
        return $this->measurementList->getMeasurementWithIds();
    }

    public function measurementById(int $measurementId): MeasurementInterface
    {
        return $this->measurementList->getMeasurementWithIds()[$measurementId];
    }

    public function getName(): string
    {
        return 'measurement_extension';
    }
}

