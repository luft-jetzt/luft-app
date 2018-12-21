<?php declare(strict_types=1);

namespace App\Controller;

use App\Analysis\FireworksAnalysis\FireworksAnalysisInterface;
use App\Analysis\KomfortofenAnalysis\KomfortofenAnalysisInterface;
use Symfony\Component\HttpFoundation\Response;

class AnalysisController extends AbstractController
{
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

    public function fireworksAction(FireworksAnalysisInterface $fireworksAnalysis): Response
    {
        $fireworksAnalysis = $fireworksAnalysis->analyze();

        return $this->render('Analysis/fireworks.html.twig', [
            'fireworksList' => $fireworksAnalysis,
        ]);
    }
}
