<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\ScheduleBundle\Entity\Constraint;
use Zentrium\Bundle\ScheduleBundle\Form\Type\ConstraintType;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Builder\ConfigurableBuilderInterface;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Constraint as BasicConstraint;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\ConstraintInterface;

/**
 * @Route("/schedules/constraints")
 */
class ConstraintController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/new/{type}", name="schedule_constraint_new_form", requirements={"type": "[a-z0-9-]+"})
     * @Template
     */
    public function newFormAction(Request $request, $type)
    {
        $type = str_replace('-', '_', $type);

        $builder = $this->get('zentrium_schedule.schedule.constraint_registry')->getBuilder($type);
        if ($builder === null) {
            throw $this->createNotFoundException('Unknown type.');
        }

        $constraint = new BasicConstraint($type, '', $builder->initialize());

        return $this->handleEdit($request, $constraint);
    }

    /**
     * @Route("/new", name="schedule_constraint_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        $builders = $this->get('zentrium_schedule.schedule.constraint_registry')->getBuilders();

        return [
            'builders' => $builders,
            'return' => $request->query->get('return'),
        ];
    }

    /**
     * @Route("/{constraint}/edit", name="schedule_constraint_edit")
     * @Template
     */
    public function editAction(Request $request, Constraint $constraint)
    {
        $manager = $this->get('zentrium_schedule.manager.constraint');

        return $this->handleEdit($request, $manager->deserialize($constraint), $constraint);
    }

    private function handleEdit(Request $request, ConstraintInterface $constraint, Constraint $entity = null)
    {
        $builder = $this->get('zentrium_schedule.schedule.constraint_registry')->getBuilder($constraint->getType());
        if ($builder === null) {
            throw $this->createNotFoundException('Unknown type.');
        }

        $parameters = $constraint->getParameters();

        $formOptions = [];
        if ($builder instanceof ConfigurableBuilderInterface) {
            $formOptions['builder'] = function (FormBuilderInterface $formBuilder) use ($builder, $parameters) {
                $builder->buildForm($parameters, $formBuilder);
            };
        }

        $form = $this->createForm(ConstraintType::class, null, $formOptions);

        $formData = ['name' => $constraint->getName()];
        if (isset($form['parameters'])) {
            $formData['parameters'] = $form['parameters']->getData();
        }
        $form->setData($formData);

        $form->handleRequest($request);

        if ($form->isValid()) {
            if (isset($form['parameters'])) {
                $parameters = $builder->handleFormData($parameters, $form['parameters']->getData());
            }

            $newConstraint = new BasicConstraint($constraint->getType(), $form['name']->getData(), $parameters);
            $this->get('zentrium_schedule.manager.constraint')->save($newConstraint, $entity);

            $this->addFlash('success', 'zentrium_schedule.constraint.form.saved');

            $scheduleId = intval($request->query->get('return'));
            if ($scheduleId > 0) {
                return $this->redirectToRoute('schedule_validate', ['schedule' => $scheduleId]);
            } else {
                return $this->redirectToRoute('schedules');
            }
        }

        return [
            'builder' => $builder,
            'constraint' => $entity,
            'form' => $form->createView(),
        ];
    }
}
