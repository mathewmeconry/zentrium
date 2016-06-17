<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Builder;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Form\Type\DurationType;

class MaxDurationBuilder implements ConfigurableBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zentrium_schedule.constraint.max_duration.name';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'zentrium_schedule.constraint.max_duration.description';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return ['max' => 6 * 3600];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm($parameters, FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('max', DurationType::class, [
                'label' => 'zentrium_schedule.constraint.max_duration.label',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Range(['min' => 60]),
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
