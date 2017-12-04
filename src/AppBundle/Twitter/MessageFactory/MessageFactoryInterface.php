<?php

namespace AppBundle\Twitter\MessageFactory;

interface MessageFactoryInterface
{
    public function setBoxList(array $boxList = []): MessageFactoryInterface;

    public function compose(): MessageFactoryInterface;

    public function getMessage(): string;
}