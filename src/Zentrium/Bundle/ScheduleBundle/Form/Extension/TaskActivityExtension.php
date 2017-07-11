<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Extension;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\ScheduleBundle\Form\Type\TaskType;
use Zentrium\Bundle\TimesheetBundle\Entity\Activity;

class TaskActivityExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('timesheetActivity', EntityType::class, [
            'required' => false,
            'label' => 'zentrium_schedule.task.field.timesheet_activity',
            'position' => ['after' => 'skill'],
            'class' => Activity::class,
            'choice_label' => 'name',
        ]);
    }

    public function getExtendedType()
    {
        return TaskType::class;
    }
}
