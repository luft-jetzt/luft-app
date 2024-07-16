<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\Analysis\CoronaFireworksAnalysis\CoronaFireworksAnalysisInterface;
use App\Air\Analysis\CoronaFireworksAnalysis\Slot\YearSlot;
use App\Air\Analysis\FireworksAnalysis\FireworksAnalysisInterface;
use App\Air\Analysis\KomfortofenAnalysis\KomfortofenAnalysisInterface;
use App\Air\Geocoding\RequestConverter\RequestConverterInterface;
use App\Air\SeoPage\SeoPageInterface;
use App\Air\ViewModel\PollutantViewModel;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @Feature("analysis")
 */
class AnalysisController extends AbstractController
{
    /**
     * @Feature("analysis_komfortofen")
     */
    #[Route(path: '/analysis/komfortofen', name: 'analysis_komfortofen', priority: 501)]
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
    #[Route(path: '/analysis/silvester', name: 'analysis_fireworks', priority: 502)]
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
    #[Route(path: '/analysis/corona-silvester', name: 'analysis_fireworks_corona', priority: 503)]
    public function coronaFireworksAction(Request $request, RequestConverterInterface $requestConverter, CoronaFireworksAnalysisInterface $coronaFireworksAnalysis, SeoPageInterface $seoPage): Response
    {
        $seoPage
            ->setTwitterPreviewPhoto('/img/share/silvester/twitter.jpg')
            ->setOpenGraphPreviewPhoto('/img/share/silvester/facebook.jpg')
            ->setTitle('Feinstaub aus Silvester-Feuerwerken')
            ->setDescription('Vergleiche die Feinstaub-Konzentration zum Jahreswechsel in deiner Umgebung.')
        ;

        $coord = $requestConverter->getCoordByRequest($request);

        if (!$coord) {
            return $this->render('Analysis/corona_fireworks.html.twig');
        }

        $yearSlotList = $coronaFireworksAnalysis->analyze($coord);

        $dataList = [];

        /** @var YearSlot $yearSlot */
        foreach ($yearSlotList as $year => $yearSlot) {
            /** @var PollutantViewModel $model */
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
