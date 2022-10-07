<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[ORM\Table(name: 'twitter_schedule')]
#[ORM\Entity(repositoryClass: 'App\Repository\TwitterScheduleRepository')]
#[JMS\ExclusionPolicy('ALL')]
class TwitterSchedule
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[JMS\Expose]
    #[JMS\Type("DateTime<'U'>")]
    protected $createdAt;

    #[ORM\Column(type: 'string', nullable: false)]
    #[JMS\Expose]
    protected $title;

    #[ORM\Column(type: 'string', nullable: true)]
    protected $cron = '0 * * * *';

    #[ORM\Column(type: 'float', nullable: true)]
    protected $latitude = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    protected $longitude = 0.0;

    #[ORM\ManyToOne(targetEntity: 'Station', inversedBy: 'twitterSchedules')]
    #[ORM\JoinColumn(name: 'station_id', referencedColumnName: 'id')]
    protected $station;

    #[ORM\ManyToOne(targetEntity: 'City', inversedBy: 'twitterSchedules')]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id')]
    protected $city;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): TwitterSchedule
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): TwitterSchedule
    {
        $this->title = $title;

        return $this;
    }

    public function setCron(string $cron): TwitterSchedule
    {
        $this->cron = $cron;

        return $this;
    }

    public function getCron(): ?string
    {
        return $this->cron;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude = null): TwitterSchedule
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude = null): TwitterSchedule
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(Station $station = null): TwitterSchedule
    {
        $this->station = $station;

        return $this;
    }

    public function setCity(City $city): TwitterSchedule
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }
}
