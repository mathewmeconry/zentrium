<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Builder;

use Symfony\Component\Form\FormBuilderInterface;

interface ConfigurableBuilderInterface extends BuilderInterface
{
    /**
     * Returns a form in case the parameters are user-configurable.
     *
     * @param mixed                $parameters
     * @param FormBuilderInterface $formBuilder
     */
    public function buildForm($parameters, FormBuilderInterface $formBuilder);

    /**
     * Handles a form submission and returns the updated parameters.
     *
     * @param mixed $parameters
     * @param mixed $formData
     *
     * @return mixed
     */
    public function handleFormData($parameters, $formData);
}
