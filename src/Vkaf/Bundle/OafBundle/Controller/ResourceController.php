<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Vkaf\Bundle\OafBundle\Entity\Resource;
use Vkaf\Bundle\OafBundle\Entity\ResourceAssignment;
use Vkaf\Bundle\OafBundle\Form\Type\ResourceAssignmentType;
use Vkaf\Bundle\OafBundle\Form\Type\ResourceType;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;

/**
 * @Route("/oaf/resources")
 */
class ResourceController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="oaf_resources")
     * @Template
     */
    public function indexAction()
    {
        $resources = $this->get('vkaf_oaf.manager.resource')->findAll();

        return [
            'resources' => $resources,
        ];
    }

    /**
     * @Route("/new", name="oaf_resource_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new Resource());
    }

    /**
     * @Route("/assign", name="oaf_resource_assign")
     * @Template
     */
    public function assignAction(Request $request)
    {
        $newAssignment = new ResourceAssignment();
        $newAssignment->setAssignedAt(new DateTime());
        $newAssignment->setAssignedBy($this->getUser());

        $assignForm = $this->createForm(ResourceAssignmentType::class, $newAssignment);

        $assignments = $this->get('vkaf_oaf.manager.resource_assignment')->findAll();

        $assignForm->handleRequest($request);

        if ($assignForm->isSubmitted() && $assignForm->isValid()) {
            $manager = $this->get('vkaf_oaf.manager.resource_assignment');
            $manager->save($newAssignment);

            $this->addFlash('success', 'vkaf_oaf.resource.form.assigned', [
                '%user%' => $newAssignment->getUser()->getName(),
            ]);

            return $this->redirectToRoute('oaf_resource_assign');
        }

        return [
            'assignForm' => $assignForm->createView(),
            'assignments' => $assignments,
        ];
    }

    /**
     * @Route("/{resource}/edit", name="oaf_resource_edit")
     * @Template
     */
    public function editAction(Request $request, Resource $resource)
    {
        return $this->handleEdit($request, $resource);
    }

    /**
     * @Route("/assignments/{assignment}/return", name="oaf_resource_return", options={"protect": true})
     * @Method("POST")
     */
    public function returnAction(Request $request, ResourceAssignment $assignment)
    {
        $assignment->setReturnedBy($this->getUser());
        $assignment->setReturnedAt(new DateTime());

        $manager = $this->get('vkaf_oaf.manager.resource_assignment');
        $manager->save($assignment);

        return new JsonResponse([
            'row' => $this->renderView('VkafOafBundle:Resource:returnRow.html.twig', ['assignment' => $assignment]),
        ]);
    }

    private function handleEdit(Request $request, Resource $resource)
    {
        $form = $this->createForm(ResourceType::class, $resource);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->get('vkaf_oaf.manager.resource');
            $manager->save($resource);

            $this->addFlash('success', 'vkaf_oaf.resource.form.saved');

            return $this->redirectToRoute('oaf_resources');
        }

        return [
            'resource' => $resource,
            'form' => $form->createView(),
        ];
    }
}
