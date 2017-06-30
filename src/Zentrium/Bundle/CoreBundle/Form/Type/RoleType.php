<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zentrium\Bundle\CoreBundle\Security\RoleHierarchy;

class RoleType extends AbstractType
{
    private $roles;

    public function __construct(RoleHierarchy $roles)
    {
        $this->roles = $roles;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $roles = $this->roles->all();

        $resolver->setDefaults([
            'label' => 'zentrium.form.role',
            'choices' => array_keys($roles),
            'choice_label' => function ($value, $key, $index) use ($roles) {
                return $roles[$value][0].'.label';
            },
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $unmanaged = [];

            $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($options, &$unmanaged) {
                $unmanaged = array_diff($event->getData(), $options['choices']);
            });

            $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use (&$unmanaged) {
                $event->setData(array_merge($event->getData(), $unmanaged));
            });
        }
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
