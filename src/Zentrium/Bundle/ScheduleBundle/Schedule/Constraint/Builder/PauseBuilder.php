<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Builder;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Form\Type\DurationType;

class PauseBuilder implements ConfigurableBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zentrium_schedule.constraint.pause.name';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'zentrium_schedule.constraint.pause.description';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [
            'limit' => 4 * 3600,
            'pause' => 1 * 3600,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm($parameters, FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('limit', DurationType::class, [
                'label' => 'zentrium_schedule.constraint.pause.label_limit',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Range(['min' => 60]),
                ],
            ])
            ->add('pause', DurationType::class, [
                'label' => 'zentrium_schedule.constraint.pause.label_pause',
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
