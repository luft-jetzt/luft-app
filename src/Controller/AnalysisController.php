<?php declare(strict_types=1);

namespace App\Controller;

use App\Analysis\CoronaFireworksAnalysis\CoronaFireworksAnalysisInterface;
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
    public function coronaFireworksAction(Request $request, CoronaFireworksAnalysisInterface $coronaFireworksAnalysis): Response
    {
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');

        if (!$latitude || !$longitude) {
            return $this->render('Analysis/corona_fireworks.html.twig');
        }

        $coord = new Coord((float) $latitude, (float) $longitude);

        $dataList = $coronaFireworksAnalysis->analyze($coord);

        $newDataList = [];

        foreach ($dataList as $year => $hourList) {
            foreach ($hourList as $timestamp => $dataSet) {
                if (!array_key_exists($timestamp, $newDataList)) {
                    $newDataList[$timestamp] = [];
                }

                $newDataList[$timestamp][$year] = $dataSet;
            }
        }

        return $this->render('Analysis/corona_fireworks.html.twig', [
            'data_list' => $newDataList,
            'years' => array_keys($dataList),
        ]);
    }
}
