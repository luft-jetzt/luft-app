<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[ORM\Table(name: 'city')]
#[ORM\Entity(repositoryClass: 'App\Repository\CityRepository')]
#[JMS\ExclusionPolicy('ALL')]
class City implements \Stringable
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
    protected $name;

    #[ORM\Column(type: 'string', nullable: false)]
    #[JMS\Expose]
    protected $slug;

    #[ORM\Column(type: 'string', nullable: true)]
    #[JMS\Expose]
    protected $description;

    #[ORM\OneToMany(targetEntity: 'Station', mappedBy: 'city')]
    protected $stations;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[JMS\Expose]
    protected ?int $openWeatherMapCityId = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->stations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): City
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): City
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): City
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description = null): City
    {
        $this->description = $description;

        return $this;
    }

    public function addStation(Station $station): City
    {
        $this->stations->add($station);

        return $this;
    }

    public function getStations(): Collection
    {
        return $this->stations;
    }

    public function setStations(Collection $stations): City
    {
        $this->stations = $stations;

        return $this;
    }

    public function removeStations(Station $station): City
    {
        $this->stations->removeElement($station);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?: '';
    }

    public function getOpenWeatherMapCityId(): ?int
    {
        return $this->openWeatherMapCityId;
    }

    public function setOpenWeatherMapCityId(?int $openWeatherMapCityId): self
    {
        $this->openWeatherMapCityId = $openWeatherMapCityId;

        return $this;
    }
}
