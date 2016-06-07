<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\ScheduleBundle\Entity\Task;
use Zentrium\Bundle\ScheduleBundle\Form\Type\TaskType;

/**
 * @Route("/schedules/tasks")
 */
class TaskController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="schedule_tasks")
     * @Template
     */
    public function indexAction()
    {
        $tasks = $this->get('zentrium_schedule.manager.task')->findAll();

        return [
            'tasks' => $tasks,
        ];
    }

    /**
     * @Route("/new", name="schedule_task_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new Task());
    }

    /**
     * @Route("/{task}/edit", name="schedule_task_edit")
     * @Template
     */
    public function editAction(Request $request, Task $task)
    {
        return $this->handleEdit($request, $task);
    }

    private function handleEdit(Request $request, Task $task)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('zentrium_schedule.manager.task');
            $manager->save($task);

            $this->addFlash('success', 'zentrium_schedule.task.form.saved');

            return $this->redirectToRoute('schedule_tasks');
        }

        return [
            'task' => $task,
            'form' => $form->createView(),
        ];
    }
}
