<?php declare(strict_types=1);

namespace App\Controller;

use App\Analysis\KomfortofenAnalysis\KomfortofenAnalysisInterface;
use App\Util\DateTimeUtil;
use Symfony\Component\HttpFoundation\Response;

class AnalysisController extends AbstractController
{
    public function komfortofenAction(KomfortofenAnalysisInterface $komfortofenAnalysis): Response
    {
        $until = new \DateTimeImmutable();
        $interval = new \DateInterval('PT48H');
        $from = $until->sub($interval);

        $komfortofenAnalysis
            ->setFromDateTime($from)
            ->setUntilDateTime($until);

        $ofens = $komfortofenAnalysis->analyze();

        return $this->render('Analysis/komfortofen.html.twig', [
            'ofenList' => $ofens,
        ]);
    }
}
