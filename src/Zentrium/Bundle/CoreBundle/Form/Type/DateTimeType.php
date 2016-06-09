<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'datepicker-input', 'data-date-format' => 'dd.mm.yyyy'],
            'date_widget' => 'single_text',
            'date_format' => 'dd.MM.yyyy',
            'time_widget' => 'single_text',
            'time_format' => 'HH:mm',
        ]);
    }

    public function getParent()
    {
        return BaseDateTimeType::class;
    }
}
