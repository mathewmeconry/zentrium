<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ConstraintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'zentrium_schedule.constraint.field.name',
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);

        if (isset($options['builder'])) {
            $parametersBuilder = $builder->getFormFactory()->createNamedBuilder('parameters', FormType::class, null, [
                'label' => false,
                'constraints' => [
                    new Assert\Valid(),
                ],
            ]);

            $options['builder']($parametersBuilder);

            $builder->add($parametersBuilder);
        }

        $builder->add('save', SubmitType::class, [
            'label' => 'zentrium.form.save',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'builder' => null,
        ]);
    }
}
