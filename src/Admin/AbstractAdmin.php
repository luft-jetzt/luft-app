<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\User;
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

    protected function getUser(): User
    {
        return $this->get('security.token_storage')->getToken()->getUser();
    }
}
