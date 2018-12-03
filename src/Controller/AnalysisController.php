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

        $exceedance = $komfortofenAnalysis->analyze();

        var_dump($exceedance);

        return new Response('foo');
    }
}
