<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\ScheduleBundle\Entity\Task;

class ShiftEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('task', EntityType::class, [
                'class' => Task::class,
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
            ])
            ->add('from', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('to', DateTimeType::class, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'shift';
    }
}
