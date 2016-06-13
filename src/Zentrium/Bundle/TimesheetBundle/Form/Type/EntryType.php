<?php

namespace Zentrium\Bundle\TimesheetBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;
use Zentrium\Bundle\CoreBundle\Form\Type\DateTimeType;
use Zentrium\Bundle\TimesheetBundle\Entity\Activity;
use Zentrium\Bundle\TimesheetBundle\Entity\ActivityRepository;

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'label' => 'zentrium_timesheet.entry.field.user',
                'class' => User::class,
                'query_builder' => function (UserRepository $repo) {
                    return $repo->createSortedQueryBuilder();
                },
                'choice_label' => function (User $user) {
                    return $user->getName(true);
                },
                'placeholder' => '',
            ])
            ->add('start', DateTimeType::class, [
                'label' => 'zentrium_timesheet.entry.field.start',
            ])
            ->add('end', DateTimeType::class, [
                'label' => 'zentrium_timesheet.entry.field.end',
            ])
            ->add('activity', EntityType::class, [
                'label' => 'zentrium_timesheet.entry.field.activity',
                'class' => Activity::class,
                'query_builder' => function (ActivityRepository $repo) {
                    return $repo->createSortedQueryBuilder();
                },
                'choice_label' => 'name',
                'placeholder' => '',
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'zentrium_timesheet.entry.field.notes',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ]);
    }
}
