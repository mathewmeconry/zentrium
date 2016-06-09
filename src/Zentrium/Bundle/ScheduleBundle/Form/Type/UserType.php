<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\ScheduleBundle\Entity\Skill;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('skills', EntityType::class, [
                'required' => false,
                'label' => 'zentrium_schedule.user.field.skills',
                'multiple' => true,
                'class' => Skill::class,
                'choice_label' => 'name',
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'label' => 'zentrium_schedule.user.field.notes',
            ])
        ;
    }
}
