<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'zentrium_schedule.skill.field.name',
            ])
            ->add('shortName', TextType::class, [
                'label' => 'zentrium_schedule.skill.field.short_name',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ]);
    }
}
