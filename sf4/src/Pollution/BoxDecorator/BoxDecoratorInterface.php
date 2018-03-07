<?php declare(strict_types=1);

namespace AppBundle\Pollution\BoxDecorator;

interface BoxDecoratorInterface
{
    public function setBoxList(array $boxList): BoxDecoratorInterface;
    public function getBoxList(): array;
    public function decorate(): BoxDecoratorInterface;
}
