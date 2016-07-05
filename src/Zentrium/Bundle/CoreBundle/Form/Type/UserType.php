<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('gender', GenderType::class, [
                'required' => false,
            ])
            ->add('birthday', DateType::class, [
                'label' => 'zentrium.user.field.birthday',
                'required' => false,
            ])
            ->add('mobilePhone', PhoneNumberType::class, [
                'label' => 'zentrium.user.field.mobile_phone',
                'required' => false,
            ])
            ->add('bednumber', NumberType::class, [
                'label' => 'zentrium.user.field.bednumber',
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'zentrium.user.field.title',
                'required' => false,
            ])
            ->add('groups', EntityType::class, [
                'required' => false,
                'label' => 'zentrium.user.field.groups',
                'multiple' => true,
                'class' => Group::class,
                'choice_label' => 'name',
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
