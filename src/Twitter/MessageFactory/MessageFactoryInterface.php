<?php declare(strict_types=1);

namespace App\Twitter\MessageFactory;

interface MessageFactoryInterface
{
    public function setPollutantList(array $pollutantList = []): MessageFactoryInterface;

    public function setTitle(string $title = ''): MessageFactoryInterface;

    public function setLink(string $link = ''): MessageFactoryInterface;

    public function compose(): MessageFactoryInterface;

    public function getMessage(): string;

    public function reset(): MessageFactoryInterface;
}
