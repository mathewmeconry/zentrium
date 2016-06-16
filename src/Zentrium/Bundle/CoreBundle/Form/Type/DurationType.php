<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Form\DataTransformer\DurationToTextTransformer;

class DurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new DurationToTextTransformer();
        $builder->addModelTransformer($transformer);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
