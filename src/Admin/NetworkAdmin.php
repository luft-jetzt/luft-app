<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NetworkAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name', TextType::class, ['required' => true])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('link', TextType::class, ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('description')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('name');
    }
}
