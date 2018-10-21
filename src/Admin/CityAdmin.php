<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CityAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'name',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('City', ['class' => 'col-xs-6'])
            ->add('name')
            ->add('slug')
            ->add('description', TextareaType::class, ['required' => false])
            ->end()

            ->with('User', ['class' => 'col-xs-6'])
            ->add('user', EntityType::class, [
                    'class' => User::class,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('createdAt')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('user')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                ]
            ])
        ;
    }
}
