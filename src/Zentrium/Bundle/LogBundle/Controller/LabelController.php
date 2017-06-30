<?php

namespace Zentrium\Bundle\LogBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\LogBundle\Entity\Label;
use Zentrium\Bundle\LogBundle\Form\Type\LabelType;

class LabelController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/logs/labels/new", name="log_label_new")
     * @Secure("ROLE_LOG_WRITE")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new Label());
    }

    /**
     * @Route("/logs/labels/{label}/edit", name="log_label_edit")
     * @Secure("ROLE_LOG_WRITE")
     * @Template
     */
    public function editAction(Request $request, Label $label)
    {
        return $this->handleEdit($request, $label);
    }

    private function handleEdit(Request $request, Label $label)
    {
        $form = $this->createForm(LabelType::class, $label);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($label);
            $em->flush();

            $this->addFlash('success', 'zentrium_log.label.form.saved');

            return $this->redirectToRoute('logs');
        }

        return [
            'label' => $label,
            'form' => $form->createView(),
        ];
    }
}
