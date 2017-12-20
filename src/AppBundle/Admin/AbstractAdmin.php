<?php

namespace AppBundle\Admin;

use Psr\Container\ContainerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin as SonataAbstractAdmin;

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

}