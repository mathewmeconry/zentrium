<?php

namespace Zentrium\Bundle\LogBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Form\Type\DateTimeType;
use Zentrium\Bundle\LogBundle\Entity\Label;
use Zentrium\Bundle\LogBundle\Entity\LabelRepository;
use Zentrium\Bundle\LogBundle\Entity\Log;

class LogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'zentrium_log.log.field.title',
            ])
            ->add('details', TextareaType::class, [
                'label' => 'zentrium_log.log.field.details',
                'required' => false,
            ])
            ->add('source', TextType::class, [
                'label' => 'zentrium_log.log.field.source',
                'required' => false,
            ])
            ->add('deadline', DateTimeType::class, [
                'label' => 'zentrium_log.log.field.deadline',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'zentrium_log.log.field.status',
                'choices' => Log::getStatuses(),
                'choice_label' => function ($value, $key, $index) {
                    return 'zentrium_log.status.'.$value;
                },
            ])
            ->add('labels', EntityType::class, [
                'label' => 'zentrium_log.log.field.labels',
                'required' => false,
                'class' => Label::class,
                'query_builder' => function (LabelRepository $repo) {
                    return $repo->createSortedQueryBuilder();
                },
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ]);
    }
}
