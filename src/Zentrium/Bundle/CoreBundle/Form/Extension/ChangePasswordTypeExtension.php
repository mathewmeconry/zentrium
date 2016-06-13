<?php

namespace Zentrium\Bundle\CoreBundle\Form\Extension;

use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('save', SubmitType::class, [
            'label' => 'zentrium.form.save',
        ]);
    }

    public function getExtendedType()
    {
        return ChangePasswordFormType::class;
    }
}
