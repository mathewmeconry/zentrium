<?php

namespace Vkaf\Bundle\OafBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;

class UserDeskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'required' => true,
                'label' => false,
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getName(true);
                },
                'query_builder' => function (UserRepository $repository) {
                    return $repository->createSortedQueryBuilder();
                },
            ])
            ->add('open', SubmitType::class, [
                'label' => 'vkaf_oaf.dashboard.user_desk.open',
            ]);
    }
}
