<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\ScheduleBundle\Entity\Task;

class SetOperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('task', EntityType::class, [
                'class' => Task::class,
            ])
            ->add('from', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('to', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('count', IntegerType::class)
        ;
    }
}
