<?php declare(strict_types=1);

namespace AppBundle\Twitter\MessageFactory;

abstract class AbstractMessageFactory implements MessageFactoryInterface
{
    /** @var array $boxList */
    protected $boxList = [];

    /** @var string $message */
    protected $message = '';

    /** @var string $title */
    protected $title = '';

    /** @var string $link */
    protected $link = '';

    public function setBoxList(array $boxList = []): MessageFactoryInterface
    {
        $this->boxList = $boxList;

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
        $this->boxList = [];

        return $this;
    }
}
