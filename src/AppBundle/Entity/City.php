<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="city")
 * @JMS\ExclusionPolicy("ALL")
 */
class City
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitterToken;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitterSecret;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitterUsername;

    /**
     * @ORM\OneToMany(targetEntity="TwitterSchedule", mappedBy="city")
     */
    protected $twitterSchedules;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="city")
     */
    protected $stations;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="cities")
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

    public function setTwitterUsername(string $twitterUsername = null): City
    {
        $this->twitterUsername = $twitterUsername;

        return $this;
    }

    public function getTwitterUsername(): ?string
    {
        return $this->twitterUsername;
    }

    public function setTwitterToken(string $twitterToken = null): City
    {
        $this->twitterToken = $twitterToken;

        return $this;
    }

    public function getTwitterToken(): ?string
    {
        return $this->twitterToken;
    }

    public function setTwitterSecret(string $twitterSecret = null): City
    {
        $this->twitterSecret = $twitterSecret;

        return $this;
    }

    public function getTwitterSecret(): ?string
    {
        return $this->twitterSecret;
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

    public function __toString(): ?string
    {
        return $this->name ? $this->name : '';
    }
}
