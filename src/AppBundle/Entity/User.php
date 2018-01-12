<?php declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_CITY_ADMIN = 'ROLE_CITY_ADMIN';
    const ROLE_TWITTER_ADMIN = 'ROLE_TWITTER_ADMIN';
    const ROLE_STATION_ADMIN = 'ROLE_STATION_ADMIN';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $password;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * @var string $plainPassword
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="user")
     */
    protected $cities;

    /**
     * @ORM\Column(name="twitter_id", type="string", length=255, nullable=true)
     */
    protected $twitterId;

    /**
     * @ORM\Column(name="twitter_access_token", type="string", length=255, nullable=true)
     */
    protected $twitterAccessToken;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->cities = new ArrayCollection();
        $this->roles = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPlainPassword(string $plainPassword = null): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function addRole(string $role): User
    {
        $this->roles[$role] = $role;

        return $this;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function removeRole(string $role): User
    {
        if (($key = array_search($role, $this->roles)) !== false) {
            unset($this->roles[$key]);
        }

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function serialize(): string
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->twitterId,
            $this->twitterAccessToken
        ));
    }

    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->twitterId,
            $this->twitterAccessToken
            ) = unserialize($serialized);
    }

    public function eraseCredentials(): User
    {
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function addCity(City $city): User
    {
        $city->setUser($this);

        $this->cities->add($city);

        return $this;
    }

    public function setCities(Collection $cities): User
    {
        $this->cities = $cities;

        return $this;
    }

    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function removeCity(City $city): User
    {
        $city->setUser(null);

        $this->cities->removeElement($city);

        return $this;
    }


    public function setTwitterId(string $twitterId): User
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    public function getTwitterId(): ?string
    {
        return $this->twitterId;
    }

    public function setTwitterAccessToken(string $twitterAccessToken): User
    {
        $this->twitterAccessToken = $twitterAccessToken;

        return $this;
    }

    public function getTwitterAccessToken(): ?string
    {
        return $this->twitterAccessToken;
    }

    public function __toString(): string
    {
        if ($this->getUsername()) {
            return $this->getUsername();
        }

        return 'Neues Benutzerkonto';
    }
}
