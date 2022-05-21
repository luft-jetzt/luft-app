<?php declare(strict_types=1);

namespace App\Air\Measurement;

class UVIndex extends AbstractMeasurement
{
    public function __construct()
    {
        $this->unitHtml = '';
        $this->unitPlain = '';
        $this->name = 'UV-Index';
        $this->shortNameHtml = 'UV-Index';
        $this->showOnMap = false;
        $this->includeInTweets = false;
        $this->decimals = 0;
    }

    public function getIdentifier(): string
    {
        return 'uvindex';
    }
}
