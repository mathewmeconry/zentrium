<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zentrium\Bundle\CoreBundle\Form\Type\DateTimeType;
use Zentrium\Bundle\CoreBundle\Form\Type\DurationType;

class RequirementSetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'zentrium_schedule.requirement_set.field.name',
            ])
        ;

        if ($options['with_period']) {
            $builder
                ->add('begin', DateTimeType::class, [
                    'label' => 'zentrium_schedule.requirement_set.field.begin',
                ])
                ->add('end', DateTimeType::class, [
                    'label' => 'zentrium_schedule.requirement_set.field.end',
                ])
                ->add('slotDuration', DurationType::class, [
                    'label' => 'zentrium_schedule.requirement_set.field.slot_duration',
                ])
            ;
        }

        $builder
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'with_period' => false,
        ]);
    }
}
