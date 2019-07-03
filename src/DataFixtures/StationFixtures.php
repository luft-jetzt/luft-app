<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\DBAL\Types\AreaType;
use App\DBAL\Types\StationType;
use App\Entity\City;
use App\Entity\Station;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createStation(54.319766, 10.123092, 17, 'DESH007', StationType::BACKGROUND, AreaType::URBAN, 'uba_de', 'Kiel-Schützenwall Verk.', null, new \DateTime('1992-06-16'), new \DateTime('2000-05-11'));

        $this->createStation(54.344196, 10.10438, 25, 'DESH012', StationType::BACKGROUND, AreaType::SUBURBAN, 'uba_de', 'Kiel-Stadtrand', null, new \DateTime('1985-07-03'), new \DateTime('1999-06-25'));

        $this->createStation(54.326394, 10.115586, 25, 'DESH019', StationType::TRAFFIC, AreaType::URBAN, 'uba_de', 'Kiel-Westring Verk.', null, new \DateTime('1995-04-26'), new \DateTime('2005-05-09'));

        $this->createStation(54.332679, 10.136176, 10, 'DESH019', StationType::BACKGROUND, AreaType::URBAN, 'uba_de', 'Kiel Schauenburger Str.', null, new \DateTime('2000-05-11'), new \DateTime('2006-11-30'));

        $this->createStation(54.34761, 10.1056, 30, 'DESH057', StationType::BACKGROUND, AreaType::URBAN, 'uba_de', 'Kiel-Bremerskamp', null, new \DateTime('2017-06-22'), null);

        $this->createStation(54.304676, 10.135158, 8, 'DESH027', StationType::TRAFFIC, AreaType::URBAN, 'uba_de', 'Kiel-Bahnhofstr. Verk.', null, new \DateTime('2005-05-11'), null);

        $this->createStation(54.309674, 10.116563, 29, 'DESH052', StationType::BACKGROUND, AreaType::URBAN, 'uba_de', 'Kiel-Max-Planck-Str.', null, new \DateTime('2007-04-25'), new \DateTime('2017-06-22'));

        $this->createStation(54.304242, 10.122364, 18, 'DESH033', StationType::TRAFFIC, AreaType::URBAN, 'uba_de', 'Kiel-Theodor-Heuss-Ring', null, new \DateTime('2012-01-01'), null);

        $this->createStation(54.303894, 10.122691, 14, 'DESH029', StationType::TRAFFIC, AreaType::URBAN, 'uba_de', 'Kiel - Theodor-Heuss-Ring', null, new \DateTime('2006-05-17'), new \DateTime('2008-05-08'));

        $manager->flush();
    }

    protected function createStation(float $latitude, float $longitude, int $alitude, string $stationCode, string $stationType, string $areaType, string $provider, string $title, City $city = null, \DateTime $fromDate = null, \DateTime $untilDate = null): Station
    {
        $station = new Station($latitude, $longitude);
        $station
            ->setAltitude($alitude)
            ->setStationCode($stationCode)
            ->setStationType($stationType)
            ->setAreaType($areaType)
            ->setProvider($provider)
            ->setTitle($title)
            ->setCity($city)
            ->setFromDate($fromDate)
            ->setUntilDate($untilDate);

        return $station;
    }
}
