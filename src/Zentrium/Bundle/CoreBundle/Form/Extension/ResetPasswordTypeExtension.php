<?php

namespace Zentrium\Bundle\CoreBundle\Form\Extension;

use FOS\UserBundle\Form\Type\ResettingFormType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ResetPasswordTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, [
            'label' => 'zentrium.security.username',
            'disabled' => true,
            'attr' => [
                'readonly' => true,
            ],
            'position' => ['before' => 'plainPassword'],
        ]);
    }

    public function getExtendedType()
    {
        return ResettingFormType::class;
    }
}
