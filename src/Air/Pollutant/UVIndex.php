<?php declare(strict_types=1);

namespace App\Air\Pollutant;

class UVIndex extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = '';
        $this->unitPlain = '';
        $this->name = 'aktueller UV-Index';
        $this->shortNameHtml = 'aktueller UV-Index';
        $this->showOnMap = false;
        $this->includeInTweets = false;
        $this->decimals = 1;
    }

    #[\Override]
    public function getIdentifier(): string
    {
        return 'uvindex';
    }
}
