<?php declare(strict_types=1);

namespace App\Twitter\MessageFactory;

abstract class AbstractMessageFactory implements MessageFactoryInterface
{
    /** @var array $pollutantList */
    protected $pollutantList = [];

    /** @var string $message */
    protected $message = '';

    /** @var string $title */
    protected $title = '';

    /** @var string $link */
    protected $link = '';

    public function setPollutantList(array $pollutantList = []): MessageFactoryInterface
    {
        $this->pollutantList = $pollutantList;

        return $this;
    }

    public function setTitle(string $title = ''): MessageFactoryInterface
    {
        $this->title = $title;

        return $this;
    }

    public function setLink(string $link = ''): MessageFactoryInterface
    {
        $this->link = $link;

        return $this;
    }

    abstract public function compose(): MessageFactoryInterface;

    public function getMessage(): string
    {
        return $this->message;
    }

    protected function resetMessage(): AbstractMessageFactory
    {
        $this->message = '';

        return $this;
    }

    public function reset(): MessageFactoryInterface
    {
        $this->message = '';
        $this->title = '';
        $this->link = '';
        $this->pollutantList = [];

        return $this;
    }
}
