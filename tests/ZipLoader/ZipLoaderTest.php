<?php declare(strict_types=1);

namespace App\Tests\ZipLoader;

use App\CityLoader\CsvLoaderInterface;
use App\CityLoader\ZipLoader;
use PHPUnit\Framework\TestCase;

class ZipLoaderTest extends TestCase
{
    public function testCsv(): void
    {
        $csvLoader = $this->createMock(CsvLoaderInterface::class);

        $zipLoader = new ZipLoader($csvLoader);
    }

    protected function csvTestData(): string
    {
        return <<<EOCSV
#loc_id	ags	ascii	name	lat	lon	amt	plz	vorwahl	einwohner	flaeche	kz	typ	level	of	invalid
105	D	DEUTSCHLAND	Bundesrepublik Deutschland	51.16766	10.42498				82175684	357104	D		2	104	
80076	16076087	KLEINWOLSCHENDORF	Kleinwolschendorf										8	26599	0
80085	16076087	PAHREN	Pahren									Ortsteil	8	26599	0
80070	16076087	DOERTENDORF	DÃ¶rtendorf									Ortsteil	8	25089	0
80082	16076087	MEHLA	Mehla									Ortsteil	8	25089	0
80087	16076087	POELLWITZ	PÃ¶llwitz					036628				Ortsteil	7	25446	0
80068	16076087	COSSENGRUEN	CossengrÃ¼n					036621					7	25446	0
80078	16076087	LAEWITZ	LÃ¤witz									Ortsteil	8	26599	0
80080	16076087	LEITLITZ	Leitlitz									Ortsteil	8	26599	0
EOCSV;
    }
}