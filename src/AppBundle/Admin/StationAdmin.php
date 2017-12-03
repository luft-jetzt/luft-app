<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class StationAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('stationCode')
            ->add('stateCode')
            ->add('title')
            ->add('latitude')
            ->add('longitude')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
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
            ->add('title')
            ->add('latitude')
            ->add('longitude')
        ;
    }
}
