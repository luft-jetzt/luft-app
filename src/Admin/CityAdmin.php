<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CityAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'name',
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('City', ['class' => 'col-xs-6'])
            ->add('name', TextType::class, ['required' => true])
            ->add('slug', TextType::class, ['required' => true])
            ->add('description', TextareaType::class, ['required' => false])
            ->end()

            ->with('User', ['class' => 'col-xs-6'])
            ->add('user', EntityType::class, [
                    'class' => User::class,
                ]
            )
            ->end()

            ->with('Fahrverbote', ['class' => 'col-xs-6'])
            ->add('fahrverboteSlug', TextType::class, ['required' => false])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('createdAt')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
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
