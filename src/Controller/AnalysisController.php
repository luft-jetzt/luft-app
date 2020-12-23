<?php declare(strict_types=1);

namespace App\Controller;

use App\Analysis\FireworksAnalysis\FireworksAnalysisInterface;
use App\Analysis\KomfortofenAnalysis\KomfortofenAnalysisInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactoryInterface;
use App\SeoPage\SeoPageInterface;
use Caldera\GeoBasic\Coord\Coord;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use JMS\Serializer\Tests\Fixtures\Discriminator\Car;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;

/**
 * @Feature("analysis")
 */
class AnalysisController extends AbstractController
{
    /**
     * @Feature("analysis_komfortofen")
     */
    public function komfortofenAction(KomfortofenAnalysisInterface $komfortofenAnalysis, SeoPageInterface $seoPage): Response
    {
        $interval = new \DateInterval('P30D');
        $untilDateTime = new \DateTimeImmutable();
        $fromDateTime = $untilDateTime->sub($interval);

        $komfortofenAnalysis
            ->setFromDateTime($fromDateTime)
            ->setUntilDateTime($untilDateTime);

        $ofens = $komfortofenAnalysis->analyze();

        $seoPage->setTitle('Finde Feinstaub-Belastungen aus Holzöfen in deiner Nähe');

        return $this->render('Analysis/komfortofen.html.twig', [
            'ofenList' => $ofens,
        ]);
    }

    /**
     * @Feature("analysis_fireworks")
     */
    public function fireworksAction(FireworksAnalysisInterface $fireworksAnalysis, SeoPageInterface $seoPage): Response
    {
        $seoPage
            ->setTwitterPreviewPhoto('/img/share/silvester/twitter.jpg')
            ->setOpenGraphPreviewPhoto('/img/share/silvester/facebook.jpg')
            ->setTitle('Feinstaub aus Silvester-Feuerwerken')
            ->setDescription('Finde erhöhte Feinstaub-Konzentrationen aus Silvester-Feuerwerken');

        $fireworksAnalysis = $fireworksAnalysis->analyze();

        return $this->render('Analysis/fireworks.html.twig', [
            'fireworksList' => $fireworksAnalysis,
        ]);
    }

    /**
     * @Feature("analysis_fireworks")
     */
    public function coronaFireworksAction(Request $request, PollutionDataFactoryInterface $pollutionDataFactory): Response
    {
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');

        if (!$latitude || !$longitude) {
            return $this->render('Analysis/corona_fireworks.html.twig');
        }

        $coord = new Coord((float) $latitude, (float) $longitude);

        $yearList = $this->initYearList();

        foreach ($yearList as $year => $hourList) {
            foreach ($hourList as $timestamp => $data) {
                $dateTime = Carbon::createFromTimestamp($timestamp);

                $result = $pollutionDataFactory
                    ->setCoord($coord)
                    ->createDecoratedPollutantList($dateTime, new CarbonInterval('PT30M'), 1)
                ;

                $yearList[$year][$timestamp] = $result;
            }
        }

        return $this->render('Analysis/corona_fireworks.html.twig');
    }

    protected function initYearList(): array
    {
        $year = (new Carbon())->subDays(360);
        $yearList = [];

        for ($yearSub = 0; $yearSub <= 2; ++$yearSub) {
            $yearList[$year->year] = [];
            $year->subYear();
        }

        foreach ($yearList as $year => $hourList) {
            $startDateTimeSpec = '%d-12-31 12:00:00';
            $startDateTime = new Carbon(sprintf($startDateTimeSpec, $year));
            $endDateTime = $startDateTime->copy()->addHours(36);

            $dateTime = $endDateTime->copy();

            do {
                $yearList[$year][$dateTime->format('Y-m-d-H-i-00')] = null;
                $dateTime->subMinutes(30);
            } while ($dateTime > $startDateTime);
        }

        return $yearList;
    }
}
