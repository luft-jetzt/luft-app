<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Stringable
{
    final const ROLE_USER = 'ROLE_USER';
    final const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    protected ?string $email = null;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    protected ?string $username = null;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected ?string $password = null;

    /**
     * @ORM\Column(type="array")
     */
    protected array $roles = [];

    protected ?string $plainPassword = null;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTime $createdAt = null;

    /**
     * @ORM\OneToOne(targetEntity="City", mappedBy="user")
     */
    protected ?City $city = null;

    /**
     * @ORM\Column(name="twitter_id", type="string", length=255, nullable=true)
     */
    protected ?string $twitterId = null;

    /**
     * @ORM\Column(name="twitter_access_token", type="string", length=255, nullable=true)
     */
    protected ?string $twitterAccessToken = null;

    /**
     * @ORM\Column(name="twitter_secret", type="string", length=255, nullable=true)
     */
    protected ?string $twitterSecret = null;

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
        return serialize([$this->id, $this->email, $this->password, $this->twitterId, $this->twitterAccessToken]);
    }

    public function unserialize($serialized): void
    {
        [$this->id, $this->email, $this->password, $this->twitterId, $this->twitterAccessToken] = unserialize($serialized);
    }

    public function eraseCredentials(): User
    {
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function setCity(City $city): User
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
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

    public function setTwitterSecret(string $twitterSecret): User
    {
        $this->twitterSecret = $twitterSecret;

        return $this;
    }

    public function getTwitterSecret(): ?string
    {
        return $this->twitterSecret;
    }

    public function __toString(): string
    {
        if ($this->getUsername()) {
            return $this->getUsername();
        }

        return 'Neues Benutzerkonto';
    }
}
