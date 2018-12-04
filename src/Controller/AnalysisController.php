<?php declare(strict_types=1);

namespace App\Controller;

use App\Analysis\KomfortofenAnalysis\KomfortofenAnalysisInterface;
use App\Util\DateTimeUtil;
use Symfony\Component\HttpFoundation\Response;

class AnalysisController extends AbstractController
{
    public function komfortofenAction(KomfortofenAnalysisInterface $komfortofenAnalysis): Response
    {
        $now = new \DateTime('2018-11-30');

        $komfortofenAnalysis
            ->setFromDateTime(DateTimeUtil::getMonthStartDateTime($now))
            ->setUntilDateTime(DateTimeUtil::getMonthEndDateTime($now));

        $ofens = $komfortofenAnalysis->analyze();

        foreach ($ofens as $ofen) {
            var_dump($ofen);
        }

        return new Response('foo');
    }
}
