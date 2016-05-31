<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zentrium\Bundle\CoreBundle\Entity\Group;
use Zentrium\Bundle\CoreBundle\Entity\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'zentrium.user.field.last_name',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'zentrium.user.field.first_name',
            ])
            ->add('groups', EntityType::class, [
                'required' => false,
                'label' => 'zentrium.user.field.groups',
                'multiple' => true,
                'class' => Group::class,
                'choice_label' => 'name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'zentrium.user.field.email',
            ])
            ->add('mobilePhone', PhoneNumberType::class, [
                'label' => 'zentrium.user.field.mobile_phone',
                'required' => false,
            ])
            ->add('username', TextType::class, [
                'label' => 'zentrium.user.field.username',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'zentrium.user.field.enabled',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
