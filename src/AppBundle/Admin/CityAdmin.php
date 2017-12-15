<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CityAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('City', ['class' => 'col-xs-6'])
            ->add('name')
            ->add('slug')
            ->add('description', TextareaType::class, ['required' => false])
            ->end()

            ->with('twitter', ['class' => 'col-xs-6'])
            ->add('twitterUsername', TextType::class, ['required' => false])
            ->add('twitterToken', TextType::class, ['required' => false])
            ->add('twitterSecret', TextType::class, ['required' => false])
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
            ->add('createdAt')
            ->add('twitterUsername', 'string', [
                'template' => 'SonataAdminBundle:CRUD:list__twitter_username.html.twig'
            ])
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'twitter' => [],
                ]
            ])
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('twitter', $this->getRouterIdParameter().'/twitter')
            ->add('twitter_token', $this->getRouterIdParameter().'/twitter_token')
        ;
    }
}
