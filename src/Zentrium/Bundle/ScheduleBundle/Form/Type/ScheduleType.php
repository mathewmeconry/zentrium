<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'zentrium_schedule.schedule.field.name',
            ])
            ->add('published', CheckboxType::class, [
                'required' => false,
                'label' => 'zentrium_schedule.schedule.field.published',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ])
        ;
    }
}
