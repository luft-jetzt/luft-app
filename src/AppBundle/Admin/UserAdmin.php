<?php

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('User', ['class' => 'col-xs-6'])
            ->add('email', TextType::class, ['required' => true])
            ->end()

            ->with('Password', ['class' => 'col-xs-6'])
            ->add('plainPassword', PasswordType::class, ['required' => false])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('email')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('email')
        ;
    }

    /**
     * @param User $user
     */
    public function prePersist($user): void
    {
        $this->encodePassword($user);
    }

    /**
     * @param User $user
     */
    public function preUpdate($user): void
    {
        $this->encodePassword($user);
    }

    protected function encodePassword(User $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if ($plainPassword) {
            $factory = $this->get('security.encoder_factory');

            /** @var PasswordEncoderInterface $encoder */
            $encoder = $factory->getEncoder($user);

            // $salt will be ignored because of bcrypt
            $password = $encoder->encodePassword($plainPassword, '');

            $user->setPassword($password);
        }
    }
}
