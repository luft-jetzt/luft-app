<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\Analysis\KomfortofenAnalysis\KomfortofenAnalysisInterface;
use App\Air\SeoPage\SeoPageInterface;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;
use Symfony\Component\HttpFoundation\Response;

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
}
