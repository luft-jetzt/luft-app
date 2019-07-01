<?php declare(strict_types=1);

namespace App\Controller;

use App\Analysis\FireworksAnalysis\FireworksAnalysisInterface;
use App\Analysis\KomfortofenAnalysis\KomfortofenAnalysisInterface;
use App\SeoPage\SeoPageInterface;
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
    public function komfortofenAction(KomfortofenAnalysisInterface $komfortofenAnalysis): Response
    {
        $interval = new \DateInterval('P30D');
        $untilDateTime = new \DateTimeImmutable();
        $fromDateTime = $untilDateTime->sub($interval);

        $komfortofenAnalysis
            ->setFromDateTime($fromDateTime)
            ->setUntilDateTime($untilDateTime);

        $ofens = $komfortofenAnalysis->analyze();

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
}
