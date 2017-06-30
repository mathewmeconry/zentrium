<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Entity\User;

class UserAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'zentrium.user.field.email',
                'position' => ['before' => 'mobilePhone'],
            ])
            ->add('username', TextType::class, [
                'label' => 'zentrium.user.field.username',
                'position' => ['after' => 'firstName'],
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'zentrium.user.field.enabled',
                'required' => false,
                'position' => ['after' => 'username'],
            ])
            ->add('roles', RoleType::class, [
                'required' => false,
                'multiple' => true,
                'position' => ['after' => 'enabled'],
            ])
        ;
    }

    public function getParent()
    {
        return UserType::class;
    }
}
