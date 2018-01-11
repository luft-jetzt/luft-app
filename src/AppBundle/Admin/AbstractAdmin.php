<?php declare(strict_types=1);

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin as SonataAbstractAdmin;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractAdmin extends SonataAbstractAdmin
{
    protected function getContainer(): ContainerInterface
    {
        return $this->getConfigurationPool()->getContainer();
    }

    protected function get(string $id)
    {
        return $this->getContainer()->get($id);
    }

    protected function isRoleGranted(string $role): bool
    {
        return $this->get('security.authorization_checker')->isGranted($role);
    }

    protected function getUser(): User
    {
        return $this->get('security.token_storage')->getToken()->getUser();
    }
}
