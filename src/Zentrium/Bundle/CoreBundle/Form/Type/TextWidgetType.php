<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Dashboard\Position;

class TextWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'zentrium.text_widget.field.title',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'zentrium.text_widget.field.content',
            ])
            ->add('position', ChoiceType::class, [
                'label' => 'zentrium.text_widget.field.position',
                'choices' => Position::all(),
                'choice_label' => function ($value) {
                    return 'zentrium.dashboard.position.'.$value;
                },
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'zentrium.text_widget.field.priority',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ])
        ;
    }
}
