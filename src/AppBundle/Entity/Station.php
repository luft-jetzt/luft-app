<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity()
 * @ORM\Table(name="station")
 * @UniqueEntity("stationCode")
 */
class Station
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=12, nullable=false, unique=true)
     */
    protected $stationCode;

    /**
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    protected $stateCode;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $title;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $longitude;

    public function getId(): int
    {
        return $this->id;
    }

    public function getStationCode(): string
    {
        return $this->stationCode;
    }

    public function setStationCode(string $stationCode): Station
    {
        $this->stationCode = $stationCode;

        return $this;
    }

    public function getStateCode(): string
    {
        return $this->stateCode;
    }

    public function setStateCode(string $stateCode): Station
    {
        $this->stateCode = $stateCode;

        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): Station
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): Station
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Station
    {
        $this->title = $title;

        return $this;
    }
}
