<?php declare(strict_types=1);

namespace App\Entity;

use App\StaticMap\StaticMapableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @ORM\Table(name="city")
 * @JMS\ExclusionPolicy("ALL")
 */
class City implements StaticMapableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @JMS\Expose()
     * @JMS\Type("DateTime<'U'>")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @JMS\Expose()
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @JMS\Expose()
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose()
     */
    protected $description;

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
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose()
     */
    protected $fahrverboteSlug;

    /**
     * @ORM\OneToMany(targetEntity="TwitterSchedule", mappedBy="city")
     */
    protected $twitterSchedules;

    /**
     * @ORM\OneToMany(targetEntity="Station", mappedBy="city")
     */
    protected $stations;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="city")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->twitterSchedules = new ArrayCollection();
        $this->stations = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): City
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): City
    {
        $this->longitude = $longitude;

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

    public function addTwitterSchedule(TwitterSchedule $twitterSchedule): City
    {
        $this->twitterSchedules->add($twitterSchedule);

        return $this;
    }

    public function getTwitterSchedules(): Collection
    {
        return $this->twitterSchedules;
    }

    public function setTwitterSchedules(Collection $twitterSchedules): City
    {
        $this->twitterSchedules = $twitterSchedules;

        return $this;
    }

    public function removeTwitterSchedule(TwitterSchedule $twitterSchedule): City
    {
        $this->twitterSchedules->removeElement($twitterSchedule);

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

    public function setUser(User $user = null): City
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
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

    public function __toString(): ?string
    {
        return $this->name ? $this->name : '';
    }
}
