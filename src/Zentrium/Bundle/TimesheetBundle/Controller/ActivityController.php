<?php

namespace Zentrium\Bundle\TimesheetBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\TimesheetBundle\Entity\Activity;
use Zentrium\Bundle\TimesheetBundle\Form\Type\ActivityType;

/**
 * @Route("/timesheet/activities")
 */
class ActivityController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="timesheet_activities")
     * @Template
     */
    public function indexAction()
    {
        $activities = $this->get('zentrium_timesheet.manager.activity')->findAll();

        return [
            'activities' => $activities,
        ];
    }

    /**
     * @Route("/new", name="timesheet_activity_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new Activity());
    }

    /**
     * @Route("/{activity}/edit", name="timesheet_activity_edit")
     * @Template
     */
    public function editAction(Request $request, Activity $activity)
    {
        return $this->handleEdit($request, $activity);
    }

    private function handleEdit(Request $request, Activity $activity)
    {
        $form = $this->createForm(ActivityType::class, $activity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->get('zentrium_timesheet.manager.activity');
            $manager->save($activity);

            $this->addFlash('success', 'zentrium_timesheet.activity.form.saved');

            return $this->redirectToRoute('timesheet_activities');
        }

        return [
            'activity' => $activity,
            'form' => $form->createView(),
        ];
    }
}
