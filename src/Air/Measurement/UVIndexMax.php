<?php declare(strict_types=1);

namespace App\Air\Measurement;

class UVIndexMax extends AbstractMeasurement
{
    public function __construct()
    {
        $this->unitHtml = '';
        $this->unitPlain = '';
        $this->name = 'maximaler UV-Index';
        $this->shortNameHtml = 'UV-Index Max';
        $this->showOnMap = false;
        $this->includeInTweets = false;
        $this->decimals = 1;
    }

    #[\Override]
    public function getIdentifier(): string
    {
        return 'uvindex_max';
    }
}
