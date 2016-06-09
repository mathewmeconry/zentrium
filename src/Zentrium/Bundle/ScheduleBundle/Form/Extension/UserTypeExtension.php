<?php

namespace Zentrium\Bundle\ScheduleBundle\Form\Extension;

use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Form\Type\UserType as BaseUserType;
use Zentrium\Bundle\ScheduleBundle\Entity\UserManager;
use Zentrium\Bundle\ScheduleBundle\Form\Type\UserType;

class UserTypeExtension extends AbstractTypeExtension
{
    private $userManager;
    private $router;

    public function __construct(UserManager $userManager, RouterInterface $router)
    {
        $this->userManager = $userManager;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->userManager->findOneByBase($builder->getData());

        $builder->add('schedule', UserType::class, [
            'label' => false,
            'mapped' => false,
            'position' => ['before' => 'save'],
            'data' => $user,
            'constraints' => [
                new Assert\Valid(),
            ],
        ]);
    }

    public function onSuccess(FormEvent $event)
    {
        $user = $event->getForm()->get('schedule')->getData();

        $this->userManager->save($user);

        if ($event->getRequest()->query->has('schedule')) {
            $event->setResponse(new RedirectResponse($this->router->generate('schedule_users')));
        }
    }

    public function getExtendedType()
    {
        return BaseUserType::class;
    }
}
