<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Entity\TextWidget;
use Zentrium\Bundle\CoreBundle\Form\Type\TextWidgetType;

/**
 * @Route("/dashboard/widgets/text")
 */
class TextWidgetController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/new", name="widget_text_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new TextWidget());
    }

    /**
     * @Route("/{widget}/edit", name="widget_text_edit")
     * @Template
     */
    public function editAction(Request $request, TextWidget $widget)
    {
        return $this->handleEdit($request, $widget);
    }

    private function handleEdit(Request $request, TextWidget $widget)
    {
        $form = $this->createForm(TextWidgetType::class, $widget);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('zentrium.manager.text_widget');
            $manager->save($widget);

            $this->addFlash('success', 'zentrium.text_widget.form.saved');

            return $this->redirectToRoute('home');
        }

        return [
            'widget' => $widget,
            'form' => $form->createView(),
        ];
    }
}
