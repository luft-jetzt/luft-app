<?php declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class TwitterScheduleAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Schedule', ['class' => 'col-md-6'])
            ->add('title')
            ->add('cron')
            ->end()

            ->with('Target', ['class' => 'col-md-6'])
            ->add('city')
            ->end()

            ->with('Source', ['class' => 'col-md-6'])
            ->add('station')
            ->add('latitude')
            ->add('longitude')
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('cron')
            ->add('city')
            ->add('station')
            ->add('latitude')
            ->add('longitude')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('title')
            ->add('cron')
            ->add('city')
            ->add('station')
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
