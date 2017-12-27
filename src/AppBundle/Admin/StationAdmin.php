<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class StationAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('City', ['class' => 'col-md-6'])
            ->add('city')
            ->add('title')
            ->end()

            ->with('Code', ['class' => 'col-md-6'])
            ->add('stationCode')
            ->add('stateCode')
            ->end()

            ->with('Coord')
            ->add('latitude', NumberType::class, ['required' => false])
            ->add('longitude', NumberType::class, ['required' => false])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('city')
            ->add('stationCode')
            ->add('stateCode')
            ->add('title')
            ->add('latitude')
            ->add('longitude')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('stationCode')
            ->add('stateCode')
            ->add('city')
            ->add('title')
            ->add('latitude')
            ->add('longitude')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                ]
            ])
        ;
    }
}
