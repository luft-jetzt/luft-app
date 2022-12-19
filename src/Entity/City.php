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

    #[ORM\Column(type: 'string', nullable: true)]
    #[JMS\Expose]
    protected $fahrverboteSlug;

    #[ORM\OneToMany(targetEntity: 'Station', mappedBy: 'city')]
    protected $stations;

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

    public function setStations(Collection $twitterSchedules): City
    {
        $this->stations = $twitterSchedules;

        return $this;
    }

    public function removeStations(Station $station): City
    {
        $this->stations->removeElement($station);

        return $this;
    }

    public function setFahrverboteSlug(string $fahrverboteSlug): City
    {
        $this->fahrverboteSlug = $fahrverboteSlug;

        return $this;
    }

    public function getFahrverboteSlug(): ?string
    {
        return $this->fahrverboteSlug;
    }

    public function hasFahrverbote(): bool
    {
        return $this->fahrverboteSlug !== null;
    }

    public function __toString(): string
    {
        return $this->name ?: '';
    }
}
