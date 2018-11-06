<?php declare(strict_types=1);

namespace App\Pollution\BoxDecorator;

interface BoxDecoratorInterface
{
    public function setPollutantList(array $pollutantList): BoxDecoratorInterface;
    public function getPollutantList(): array;
    public function decorate(): BoxDecoratorInterface;
}
