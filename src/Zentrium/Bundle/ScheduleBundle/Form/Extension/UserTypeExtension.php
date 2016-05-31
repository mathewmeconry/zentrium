<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Extension;

use FOS\UserBundle\Event\FormEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Zentrium\Bundle\CoreBundle\Form\Type\UserType;
use Zentrium\Bundle\CoreBundle\Util\SnapshotCollection;
use Zentrium\Bundle\ScheduleBundle\Entity\Skill;
use Zentrium\Bundle\ScheduleBundle\Entity\SkillManager;

class UserTypeExtension extends AbstractTypeExtension
{
    private $skillManager;

    public function __construct(SkillManager $skillManager)
    {
        $this->skillManager = $skillManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $builder->getData();
        $skills = $this->skillManager->findByUser($user);

        $builder->add('skills', EntityType::class, [
            'required' => false,
            'label' => 'zentrium_schedule.user.field.skills',
            'class' => Skill::class,
            'choice_label' => 'name',
            'multiple' => true,
            'mapped' => false,
            'position' => ['before' => 'save'],
            'data' => new SnapshotCollection($skills),
        ]);
    }

    public function onSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();
        $skills = $event->getForm()->get('skills')->getData();

        $this->skillManager->updateUser($user, $skills);
    }

    public function getExtendedType()
    {
        return UserType::class;
    }
}
