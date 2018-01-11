<?php

namespace AppBundle\UserProvider;

use AppBundle\Entity\City;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\UserProvider\Exception\LuftUsernameException;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;

class LuftUserProvider implements OAuthAwareUserProviderInterface
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function connect(User $user, UserResponseInterface $response): void
    {
        if (null !== $previousUser = $this->findUserByUsername($response)) {
            $previousUser = $this->setServiceData($previousUser, $response, true);

            $this->updateUser($previousUser);
        }

        $user = $this->setServiceData($user, $response);

        $this->updateUser($user);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): User
    {
        $user = $this->findUserByUsername($response);

        if (!$user) {
            $user = $this->registerUser($response);
        }

        $user = $this->setServiceData($user, $response);

        $this->assignCity($user);

        $this->updateUser($user);

        return $user;
    }

    protected function assignCity(User $user): LuftUserProvider
    {
        $citySlug = str_replace('luft_', '', $user->getUsername());

        /** @var City $city */
        $city = $this->doctrine->getRepository(City::class)->findOneBySlug($citySlug);

        if ($city) {
            $city->setUser($user);

            $this->doctrine->getManager()->flush();
        }

        return $this;
    }

    protected function registerUser(UserResponseInterface $response): User
    {
        $username = $response->getNickname();

        if (strpos($username, 'luft_') !== 0) {
            $message = sprintf('Twitter handle "%s" does not begin with "luft_"', $username);

            throw new LuftUsernameException($message);
        }

        $user = $this->createUser();

        $user = $this->setUserData($user, $response);

        return $user;
    }

    protected function setUserData(User $user, UserResponseInterface $response): User
    {
        $username = $response->getNickname() ? $response->getNickname() : $response->getUsername();
        $email = $response->getEmail() ? $response->getEmail() : $response->getUsername();

        $user
            ->setUsername($username)
            ->setEmail($email)
            ->setPassword('')
        ;

        return $user;
    }

    protected function setServiceData(User $user, UserResponseInterface $response, bool $clear = false): User
    {
        $username = $response->getUsername();

        if ($clear) {
            $user
                ->setTwitterId(null)
                ->setTwitterAccessToken(null)
            ;
        } else {
            $user
                ->setTwitterId($username)
                ->setTwitterAccessToken($response->getAccessToken())
            ;
        }

        return $user;
    }

    protected function findUserByUsername(UserResponseInterface $response): ?User
    {
        return $this->getUserRepository()->findOneBy(['twitterId' => $response->getUsername()]);
    }

    protected function getUserRepository(): UserRepository
    {
        return $this->doctrine->getRepository(User::class);
    }

    protected function updateUser(User $user): LuftUserProvider
    {
        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return $this;
    }

    protected function createUser(): User
    {
        return new User();
    }
}
