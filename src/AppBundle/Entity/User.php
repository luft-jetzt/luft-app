<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity()
 */
class User implements UserInterface, \Serializable
{
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
     * @ORM\Column(type="string", length=64)
     */
    protected $password;

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

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->cities = new ArrayCollection();
    }

    public function setUsername(string $username): User
    {
        $this->setEmail($username);

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->getEmail();
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

    public function getRoles(): array
    {
        return ['ROLE_USER', 'ROLE_ADMIN'];
    }

    public function serialize(): string
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->email,
            $this->password,
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

    public function __toString(): ?string
    {
        return $this->getUsername();
    }
}