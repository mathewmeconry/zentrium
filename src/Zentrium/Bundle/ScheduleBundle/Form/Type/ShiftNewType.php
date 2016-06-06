<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;

class ShiftNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('schedule', EntityType::class, [
                'class' => Schedule::class,
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'shift';
    }

    public function getParent()
    {
        return ShiftEditType::class;
    }
}
