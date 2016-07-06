<?php

namespace Vkaf\Bundle\OafBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Vkaf\Bundle\OafBundle\Entity\Resource;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;

class ResourceAssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('resource', EntityType::class, [
                'label' => 'vkaf_oaf.resource_assignment.field.resource',
                'class' => Resource::class,
                'choice_label' => 'label',
            ])
            ->add('user', EntityType::class, [
                'label' => 'vkaf_oaf.resource_assignment.field.user',
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getName(true);
                },
                'query_builder' => function (UserRepository $repository) {
                    return $repository->createSortedQueryBuilder();
                },
            ])
            ->add('save', SubmitType::class, [
                'label' => 'vkaf_oaf.resource.form.assign',
            ]);
    }
}
