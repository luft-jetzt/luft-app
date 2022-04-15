<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\ViewModel\MeasurementViewModel;
use App\Analysis\CoronaFireworksAnalysis\CoronaFireworksAnalysisInterface;
use App\Analysis\CoronaFireworksAnalysis\Slot\YearSlot;
use App\Analysis\FireworksAnalysis\FireworksAnalysisInterface;
use App\Analysis\KomfortofenAnalysis\KomfortofenAnalysisInterface;
use App\Geocoding\RequestConverter\RequestConverterInterface;
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
    public function coronaFireworksAction(Request $request, RequestConverterInterface $requestConverter, CoronaFireworksAnalysisInterface $coronaFireworksAnalysis): Response
    {
        $coord = $requestConverter->getCoordByRequest($request);

        if (!$coord) {
            return $this->render('Analysis/corona_fireworks.html.twig');
        }

        $yearSlotList = $coronaFireworksAnalysis->analyze($coord);

        $dataList = [];

        /** @var YearSlot $yearSlot */
        foreach ($yearSlotList as $year => $yearSlot) {
            /** @var MeasurementViewModel $model */
            foreach ($yearSlot->getList() as $slot => $model) {
                if (!array_key_exists($slot, $dataList)) {
                    $dataList[$slot] = [];
                }

                $dataList[$slot][$year] = $model;
            }
        }

        return $this->render('Analysis/corona_fireworks.html.twig', [
            'data_list' => $dataList,
            'years' => array_keys($yearSlotList),
        ]);
    }
}
