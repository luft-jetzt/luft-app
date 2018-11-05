<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Caldera\GeoBasic\Coord\Coord;
use App\DBAL\Types\StationType;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StationRepository")
 * @ORM\Table(name="station")
 * @UniqueEntity("stationCode")
 * @JMS\ExclusionPolicy("ALL")
 */
class Station extends Coord
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

    /**
     * @ORM\OneToMany(targetEntity="TwitterSchedule", mappedBy="station")
     */
    protected $twitterSchedules;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="twitterSchedules")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @JMS\Expose()
     */
    protected $fromDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @JMS\Expose()
     */
    protected $untilDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $altitude;

    /**
     * @ORM\Column(type="StationType", nullable=true)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\StationType")
     */
    protected $stationType;

    /**
     * @ORM\Column(type="AreaType", nullable=true)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\AreaType")
     */
    protected $areaType;

    public function __construct(float $latitude, float $longitude)
    {
        $this->twitterSchedules = new ArrayCollection();

        parent::__construct($latitude, $longitude);
    }

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
        return sprintf('%f,%f', $this->latitude, $this->longitude);
    }

    public function __toString()
    {
        return sprintf('%s: %s', $this->stationCode, $this->title);
    }

    public function addTwitterSchedule(TwitterSchedule $twitterSchedule): Station
    {
        $this->twitterSchedules->add($twitterSchedule);

        return $this;
    }

    public function getTwitterSchedules(): Collection
    {
        return $this->twitterSchedules;
    }

    public function setTwitterSchedules(Collection $twitterSchedules): Station
    {
        $this->twitterSchedules = $twitterSchedules;

        return $this;
    }

    public function removeTwitterSchedule(TwitterSchedule $twitterSchedule): Station
    {
        $this->twitterSchedules->removeElement($twitterSchedule);

        return $this;
    }

    public function setCity(City $city = null): Station
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function getFromDate(): ?\DateTime
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTime $fromDate = null): Station
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getUntilDate(): ?\DateTime
    {
        return $this->untilDate;
    }

    public function setUntilDate(\DateTime $untilDate = null): Station
    {
        $this->untilDate = $untilDate;

        return $this;
    }

    public function getAltitude(): ?int
    {
        return $this->altitude;
    }

    public function setAltitude(int $altitude): Station
    {
        $this->altitude = $altitude;

        return $this;
    }

    public function getStationType(): ?string
    {
        return $this->stationType;
    }

    public function setStationType(string $stationType = null): Station
    {
        $this->stationType = $stationType;

        return $this;
    }

    public function getAreaType(): ?string
    {
        return $this->areaType;
    }

    public function setAreaType(string $areaType = null): Station
    {
        $this->areaType = $areaType;

        return $this;
    }
}
