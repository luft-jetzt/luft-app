<?php declare(strict_types=1);

namespace App\Menu;

use App\Entity\User;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class AbstractBuilder
{
    protected FactoryInterface $factory;
    protected TokenStorageInterface $tokenStorage;

    public function __construct(FactoryInterface $factory, TokenStorageInterface $tokenStorage)
    {
        $this->factory = $factory;
        $this->tokenStorage = $tokenStorage;
    }

    protected function isUserLoggedIn(): bool
    {
        $user = $this->getUser();

        return null !== $user;
    }

    protected function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();

        if ($token && is_object($token->getUser())) {
            return $token->getUser();
        }

        return null;
    }
}
