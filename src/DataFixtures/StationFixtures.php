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
        $this->createStation(54.319766, 10.123092, 17, 'DESH007', StationType::BACKGROUND, AreaType::URBAN, 'uba_de', 'Kiel-Schützenwall  Verk.', null, new \DateTime('1992-06-16'), new \DateTime('2000-05-11'));
        $this->createStation(54.344196, 10.10438, 25, 'DESH012', StationType::BACKGROUND, AreaType::SUBURBAN, 'uba_de', 'Kiel-Stadtrand', null, new \DateTime('1985-07-03'), new \DateTime('1999-06-25'));
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


/*
1495	NULL	NULL	DESH019	Kiel-Westring Verk.	54.326394	10.115586	1995-04-26	2005-05-09	25	traffic	urban	uba_de
1497	NULL	NULL	DESH021	Kiel Schauenburger Str.	54.332679	10.136176	2000-05-11	2006-11-30	10	background	urban	uba_de
1503	NULL	NULL	DESH027	Kiel-Bahnhofstr. Verk.	54.304676	10.135158	2005-05-11	NULL	8	traffic	urban	uba_de
1505	NULL	NULL	DESH029	Kiel - Theodor-Heuss-Ring	54.303894	10.122691	2006-05-17	2008-05-08	14	traffic	urban	uba_de
1509	NULL	NULL	DESH033	Kiel-Max-Planck-Str.	54.309674	10.116563	2007-04-25	2017-06-22	29	background	urban	uba_de
1528	NULL	NULL	DESH052	Kiel-Theodor-Heuss-Ring	54.304242	10.122364	2012-01-01	NULL	18	traffic	urban	uba_de
1533	NULL	NULL	DESH057	Kiel-Bremerskamp	54.34761	10.1056	2017-06-22	NULL	30	background	urban	uba_de
*/