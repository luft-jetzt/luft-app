<?php declare(strict_types=1);

namespace App\Air\Measurement;

class CoronaIncidence extends AbstractMeasurement
{
    public function __construct()
    {
        $this->unitHtml = '';
        $this->unitPlain = '';
        $this->name = 'Corona-Inzidenz';
        $this->shortNameHtml = 'Corona-Inzidenz';
        $this->showOnMap = false;
        $this->includeInTweets = false;
        $this->decimals = 0;
    }

    public function getIdentifier(): string
    {
        return 'coronaincidence';
    }
}
