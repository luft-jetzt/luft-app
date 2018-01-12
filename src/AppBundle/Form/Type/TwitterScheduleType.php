<?php declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Station;
use AppBundle\Repository\StationRepository;
use Cron\CronExpression;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwitterScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $city = $options['city'];

        $builder
            ->add('title', TextType::class)
            ->add('station', EntityType::class, [
                'class' => Station::class,
                'query_builder' => function (StationRepository $sr) use ($city) {
                    $qb = $sr
                        ->createQueryBuilder('s')
                        ->orderBy('s.stationCode', 'ASC')
                    ;

                    if ($city) {
                        $qb
                            ->where($qb->expr()->eq('s.city', ':city'))
                            ->setParameter('city', $city)
                        ;
                    }

                    return $qb;
                },
            ])
            ->add('cron', ChoiceType::class, [
                'choices'  => range(0, 59),
            ]);


        $builder->get('cron')
            ->addModelTransformer(new CallbackTransformer(
                function (string $cronString) {
                    $cron = CronExpression::factory($cronString);

                    return $cron->getPreviousRunDate()->format('i');
                },
                function (string $value) {
                    return sprintf('%d * * * *', (int) $value);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'city' => null,
        ));
    }

    public function getName(): string
    {
        return 'twitter_schedule';
    }
}
