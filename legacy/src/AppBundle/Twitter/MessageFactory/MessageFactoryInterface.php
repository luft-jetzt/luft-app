<?php declare(strict_types=1);

namespace AppBundle\Twitter\MessageFactory;

interface MessageFactoryInterface
{
    public function setBoxList(array $boxList = []): MessageFactoryInterface;

    public function setTitle(string $title = ''): MessageFactoryInterface;

    public function setLink(string $link = ''): MessageFactoryInterface;

    public function compose(): MessageFactoryInterface;

    public function getMessage(): string;

    public function reset(): MessageFactoryInterface;
}