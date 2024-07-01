<?php declare(strict_types=1);

namespace App\Entity;

use Caldera\GeoBasic\Coordinate\Coordinate;
use Doctrine\Common\Collections\ArrayCollection;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;
use Jsor\Doctrine\PostGIS\Types\PostGISType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Table(name: 'station')]
#[ORM\Entity(repositoryClass: 'App\Repository\StationRepository')]
#[UniqueEntity('stationCode')]
#[ORM\HasLifecycleCallbacks]
class Station extends Coordinate
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Ignore]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 12, unique: true, nullable: false)]
    protected ?string $stationCode = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $ubaStationId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $title = null;

    #[ORM\Column(type: 'float', nullable: false)]
    protected ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: false)]
    protected ?float $longitude = null;

    #[ORM\Column(
        type: PostGISType::GEOMETRY,
        options: ['geometry_type' => 'POINT'],
    )]
    #[Ignore]
    public ?string $coord = null;

    #[ORM\ManyToOne(targetEntity: 'City', inversedBy: 'stations')]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?City $city = null;

    #[ORM\Column(type: 'date', nullable: true)]
    protected ?\DateTime $fromDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    protected ?\DateTime $untilDate = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $altitude = null;

    #[DoctrineAssert\EnumType(entity: 'App\DBAL\Types\StationType')]
    #[ORM\Column(type: 'StationType', nullable: true)]
    #[Ignore]
    protected ?string $stationType = null;

    #[DoctrineAssert\EnumType(entity: 'App\DBAL\Types\AreaType')]
    #[ORM\Column(type: 'AreaType', nullable: true)]
    #[Ignore]
    protected ?string $areaType = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Ignore]
    protected ?string $provider = null;

    #[ORM\ManyToOne(targetEntity: 'Network', inversedBy: 'stations')]
    #[ORM\JoinColumn(name: 'network_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?Network $network = null;

    #[ORM\OneToMany(targetEntity: 'Data', mappedBy: 'station')]
    #[Ignore]
    protected $datas;

    public function __construct(float $latitude, float $longitude)
    {
        $this->datas = new ArrayCollection();

        $this->coord = sprintf('POINT(%f %f)', $latitude, $longitude);

        parent::__construct($latitude, $longitude);
    }

    public function setId(int $id): Station
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStationCode(): ?string
    {
        return $this->stationCode;
    }

    public function setStationCode(string $stationCode): Station
    {
        $this->stationCode = $stationCode;

        return $this;
    }

    public function getUbaStationId()
    {
        return $this->ubaStationId;
    }

    public function setUbaStationId($ubaStationId)
    {
        $this->ubaStationId = $ubaStationId;

        return $this;
    }

    public function setCoord(string $coord): Station
    {
        $this->coord = $coord;

        return $this;
    }

    public function getCoord(): ?string
    {
        return $this->coord;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title = null): Station
    {
        $this->title = $title;

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

    public function getFromDateFormatted(): ?string
    {
        return $this->fromDate ? $this->fromDate->format('Y-m-d H:i:s') : null;
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

    public function getUntilDateFormatted(): ?string
    {
        return $this->untilDate ? $this->untilDate->format('Y-m-d H:i:s') : null;
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

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): Station
    {
        $this->provider = $provider;

        return $this;
    }

    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    public function setNetwork(Network $network): Station
    {
        $this->network = $network;

        return $this;
    }

    public function setLatitude(?float $latitude): Station
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function setLongitude(?float $longitude): Station
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    #[ORM\PrePersist]
    public function prePersist(): Station
    {
        if ($this->latitude && $this->longitude) {
            $this->coord = sprintf('POINT(%f %f)', $this->longitude, $this->latitude);
        }

        return $this;
    }

    public function getPin(): string
    {
        return sprintf('%f,%f', $this->latitude, $this->longitude);
    }
}
