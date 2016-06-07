<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\DateType as BaseDateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateType extends BaseDateType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'attr' => ['class' => 'datepicker-input', 'data-format' => 'dd.mm.yyyy'],
            'widget' => 'single_text',
            'format' => 'dd.MM.yyyy',
        ]);
    }
}
