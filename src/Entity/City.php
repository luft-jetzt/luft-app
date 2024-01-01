<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'city')]
#[ORM\Entity(repositoryClass: 'App\Repository\CityRepository')]
class City implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    protected ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'string', nullable: false)]
    protected ?string $name = null;

    #[ORM\Column(type: 'string', nullable: false)]
    protected ?string $slug = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $description = null;

    #[ORM\OneToMany(targetEntity: 'Station', mappedBy: 'city')]
    protected Collection $stations;

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
}
