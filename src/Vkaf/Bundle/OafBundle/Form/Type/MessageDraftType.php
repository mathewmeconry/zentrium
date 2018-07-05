<?php

namespace Vkaf\Bundle\OafBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;

class MessageDraftType extends AbstractType
{
    const RECEIVERS_ACTIVE = 'active';
    const RECEIVERS_CHOICE = 'choice';
    const RECEIVERS_PRESENT = 'present';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('receiverSet', ChoiceType::class, [
                'label' => 'vkaf_oaf.message.field.receivers',
                'choices' => [
                    'vkaf_oaf.message.receiver.active' => self::RECEIVERS_ACTIVE,
                    'vkaf_oaf.message.receiver.choice' => self::RECEIVERS_CHOICE,
                    'vkaf_oaf.message.receiver.present' => self::RECEIVERS_PRESENT,
                ],
                'mapped' => false,
                'data' => self::RECEIVERS_CHOICE,
            ])
            ->add('receivers', EntityType::class, [
                'required' => false,
                'label' => false,
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
