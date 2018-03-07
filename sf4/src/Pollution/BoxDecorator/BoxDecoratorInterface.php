<?php declare(strict_types=1);

namespace App\Pollution\BoxDecorator;

interface BoxDecoratorInterface
{
    public function setBoxList(array $boxList): BoxDecoratorInterface;
    public function getBoxList(): array;
    public function decorate(): BoxDecoratorInterface;
}
