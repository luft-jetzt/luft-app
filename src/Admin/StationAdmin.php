<?php declare(strict_types=1);

namespace App\Admin;

use App\DBAL\Types\AreaType;
use App\DBAL\Types\StationType;
use App\Entity\City;
use App\Repository\CityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StationAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'stationCode',
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('City', ['class' => 'col-md-6'])
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => City::class,
                'query_builder' => function (CityRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
            ])
            ->add('title', TextType::class, ['required' => true])
            ->end()

            ->with('Code', ['class' => 'col-md-6'])
            ->add('stationCode', TextType::class, ['required' => true])
            ->end()

            ->with('Coord', ['class' => 'col-md-6'])
            ->add('latitude', NumberType::class, ['required' => true])
            ->add('longitude', NumberType::class, ['required' => true])
            ->add('altitude', NumberType::class, ['required' => true])
            ->end()

            ->with('Type', ['class' => 'col-md-6'])
            ->add('stationType', ChoiceType::class, [
                'required' => true,
                'choices' => StationType::getChoices(),
                'choice_translation_domain' => 'messages',
            ])
            ->add('areaType', ChoiceType::class, [
                'required' => true,
                'choices' => AreaType::getChoices(),
                'choice_translation_domain' => 'messages',
            ])
            ->end()

            ->with('DateTime', ['class' => 'col-md-6'])
            ->add('fromDate', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('untilDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('city')
            ->add('stationCode')
            ->add('title')
            ->add('latitude')
            ->add('longitude')
            ->add('altitude')
            ->add('fromDate')
            ->add('untilDate')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('stationCode')
            ->add('city')
            ->add('title')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                ]
            ])
        ;
    }
}
