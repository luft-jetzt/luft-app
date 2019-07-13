<?php declare(strict_types=1);

namespace App\Twitter\MessageFactory;

interface ExtendedMessageFactoryInterface extends MessageFactoryInterface
{
    public function setAdditionalPollutantList(array $additionalPollutantList = []): ExtendedMessageFactoryInterface;
}
