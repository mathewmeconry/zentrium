<?php

namespace Zentrium\Bundle\TimesheetBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Entity\Group;
use Zentrium\Bundle\CoreBundle\Entity\GroupRepository;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;
use Zentrium\Bundle\CoreBundle\Form\Type\DateType;

class ExportParametersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('format', ChoiceType::class, [
                'label' => 'zentrium_timesheet.export.parameter.format',
                'choices' => [
                    'zentrium_timesheet.export.format.report' => 'report',
                    'zentrium_timesheet.export.format.csv' => 'csv',
                ],
            ])
            ->add('from', DateType::class, [
                'label' => 'zentrium_timesheet.export.parameter.from',
            ])
            ->add('to', DateType::class, [
                'label' => 'zentrium_timesheet.export.parameter.to',
            ])
            ->add('userFilter', EntityType::class, [
                'label' => 'zentrium_timesheet.export.parameter.user_filter',
                'required' => false,
                'class' => User::class,
                'query_builder' => function (UserRepository $repo) {
                    return $repo->createSortedQueryBuilder();
                },
                'choice_label' => function (User $user) {
                    return $user->getName(true);
                },
            ])
            ->add('groupFilter', EntityType::class, [
                'label' => 'zentrium_timesheet.export.parameter.group_filter',
                'required' => false,
                'class' => Group::class,
                'query_builder' => function (GroupRepository $repo) {
                    return $repo->createSortedQueryBuilder();
                },
                'choice_label' => 'name',
            ])
            ->add('export', SubmitType::class, [
                'label' => 'zentrium_timesheet.export.form.submit',
            ]);
    }
}
