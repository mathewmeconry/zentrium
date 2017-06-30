<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zentrium\Bundle\CoreBundle\Entity\Group;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'zentrium.group.field.name',
            ])
            ->add('shortName', TextType::class, [
                'label' => 'zentrium.group.field.short_name',
            ])
            ->add('roles', RoleType::class, [
                'required' => false,
                'multiple' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
