<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\CoreBundle\Entity\User as BaseUser;
use Zentrium\Bundle\ScheduleBundle\Entity\Availability;
use Zentrium\Bundle\ScheduleBundle\Entity\User;
use Zentrium\Bundle\ScheduleBundle\Form\Type\AvailabilityType;

/**
 * @Route("/schedules/users")
 */
class UserController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="schedule_users")
     * @Template
     */
    public function indexAction()
    {
        $users = $this->get('zentrium_schedule.manager.user')->findAll();

        return [
            'users' => $users,
        ];
    }

    /**
     * @Route("/{user}/availability", name="schedule_user_availability")
     * @ParamConverter("base", options={"id" = "user"})
     * @Template
     */
    public function availabilityAction(Request $request, BaseUser $base)
    {
        $user = $this->get('zentrium_schedule.manager.user')->findOneByBase($base);

        return [
            'user' => $user,
        ];
    }

    /**
     * @Route("/{user}/availability/new", name="schedule_user_availability_new")
     * @ParamConverter("base", options={"id" = "user"})
     * @Template
     */
    public function newAvailabilityAction(Request $request, BaseUser $base)
    {
        $user = $this->get('zentrium_schedule.manager.user')->findOneByBase($base);

        $availability = new Availability();
        $availability->setUser($user);

        return $this->handleAvailabilityEdit($request, $availability);
    }

    /**
     * @Route("/availability/{availability}/edit", name="schedule_user_availability_edit")
     * @ParamConverter("base", options={"id" = "user"})
     * @Template
     */
    public function editAvailabilityAction(Request $request, Availability $availability)
    {
        return $this->handleAvailabilityEdit($request, $availability);
    }

    private function handleAvailabilityEdit(Request $request, Availability $availability)
    {
        $form = $this->createForm(AvailabilityType::class, $availability);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->get('zentrium_schedule.manager.availability');
            $manager->save($availability);

            $this->addFlash('success', 'zentrium_schedule.availability.form.saved', [
                '%user%' => $availability->getUser()->getBase()->getName(),
            ]);

            return $this->redirectToRoute('schedule_user_availability', ['user' => $availability->getUser()->getBase()->getId()]);
        }

        return [
            'availability' => $availability,
            'form' => $form->createView(),
        ];
    }
}
