<?php

namespace Zentrium\Bundle\TimesheetBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Entity\User;

class EntryApprovalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('approvedBy', EntityType::class, [
                'class' => User::class,
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'approval';
    }
}
