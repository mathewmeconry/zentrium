<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Builder;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Form\Type\DurationType;

class SleepBuilder implements ConfigurableBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zentrium_schedule.constraint.sleep.name';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'zentrium_schedule.constraint.sleep.description';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [
            'minimum' => 8 * 3600,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm($parameters, FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('minimum', DurationType::class, [
                'label' => 'zentrium_schedule.constraint.sleep.label',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Range(['min' => 3600]),
                ],
            ])
            ->setData($parameters)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function handleFormData($parameters, $formData)
    {
        return $formData;
    }
}
