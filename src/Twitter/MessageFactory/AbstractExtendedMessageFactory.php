<?php declare(strict_types=1);

namespace App\Twitter\MessageFactory;

abstract class AbstractExtendedMessageFactory extends AbstractMessageFactory implements ExtendedMessageFactoryInterface
{
    protected array $additionalPollutantList = [];

    public function setAdditionalPollutantList(array $additionalPollutantList = []): ExtendedMessageFactoryInterface
    {
        $this->additionalPollutantList = $additionalPollutantList;

        return $this;
    }
}
