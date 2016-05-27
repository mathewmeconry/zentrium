<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Form\Type\ColorType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'zentrium_schedule.task.field.name',
            ])
            ->add('code', TextType::class, [
                'label' => 'zentrium_schedule.task.field.code',
            ])
            ->add('color', ColorType::class)
            ->add('notes', TextareaType::class, [
                'required' => false,
                'label' => 'zentrium_schedule.task.field.notes',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ]);
    }
}
