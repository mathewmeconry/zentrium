<?php

namespace Vkaf\Bundle\OafBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;

class MessageDraftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('receivers', EntityType::class, [
                'label' => 'vkaf_oaf.message.field.receivers',
                'class' => User::class,
                'multiple' => true,
                'choice_label' => function (User $user) {
                    return $user->getName(true);
                },
                'query_builder' => function (UserRepository $repository) {
                    return $repository->createSortedQueryBuilder();
                },
            ])
            ->add('text', TextareaType::class, [
                'label' => 'vkaf_oaf.message.field.text',
            ])
            ->add('send', SubmitType::class, [
                'label' => 'vkaf_oaf.message.form.send',
            ])
        ;
    }
}
