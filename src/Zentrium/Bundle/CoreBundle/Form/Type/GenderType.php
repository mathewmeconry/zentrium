<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenderType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'zentrium.form.gender',
            'choices' => [
                'zentrium.gender.female' => 'female',
                'zentrium.gender.male' => 'male',
            ],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
