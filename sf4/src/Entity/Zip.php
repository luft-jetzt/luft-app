<?php declare(strict_types=1);

namespace App\Entity;

use Caldera\GeoBasic\Coord\Coord;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="zip")
 */
class Zip extends Coord
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false, length=5)
     */
    protected $zip;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $longitude;

    public function getId(): int
    {
        return $this->id;
    }

    public function setZip(string $zip): Zip
    {
        $this->zip = $zip;

        return $this;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setLatitude(float $latitude): Zip
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude): Zip
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
