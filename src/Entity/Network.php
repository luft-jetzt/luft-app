<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'network')]
#[ORM\Entity]
class Network
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'string', nullable: true)]
    protected $name;

    #[ORM\Column(type: 'text', nullable: true)]
    protected $description;

    #[ORM\Column(type: 'string', nullable: true)]
    protected $link;

    #[ORM\OneToMany(targetEntity: 'Station', mappedBy: 'network')]
    protected $stations;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Network
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Network
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): Network
    {
        $this->description = $description;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): Network
    {
        $this->link = $link;

        return $this;
    }

    public function setStations(Collection $stations): Network
    {
        $this->stations = $stations;

        return $this;
    }

    public function getStations(): Collection
    {
        return $this->stations;
    }
}
