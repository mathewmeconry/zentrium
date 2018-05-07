<?php

namespace Vkaf\Bundle\OafBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PushSubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('endpoint', TextType::class)
            ->add('key', TextType::class)
            ->add('token', TextType::class)
        ;
    }
}
