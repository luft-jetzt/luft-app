<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as JMS;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StationRepository")
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
     * @JMS\Expose()
     */
    protected $stationCode;

    /**
     * @ORM\Column(type="string", length=2, nullable=false)
     * @JMS\Expose()
     */
    protected $stateCode;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @JMS\Expose()
     */
    protected $title;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @JMS\Expose()
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @JMS\Expose()
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

    public function getPin(): string
    {
        return $this->latitude . ',' . $this->longitude;
    }
}
