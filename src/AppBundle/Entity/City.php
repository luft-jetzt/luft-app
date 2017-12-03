<?php

namespace AppBundle\Entity;

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

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): City
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): City
    {
        $this->name = $name;

        return $this;
    }
}
