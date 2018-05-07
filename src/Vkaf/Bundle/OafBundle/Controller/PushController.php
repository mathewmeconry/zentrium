<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Vkaf\Bundle\OafBundle\Entity\PushSubscription;
use Vkaf\Bundle\OafBundle\Form\Type\PushSubscribeType;
use Vkaf\Bundle\OafBundle\Form\Type\PushUnsubscribeType;

/**
 * @Route("/oaf/push")
 */
class PushController extends Controller
{
    /**
     * @Route("/subscribe", name="oaf_push_subscribe", options={"protect": true})
     */
    public function subscribeAction(Request $request)
    {
        $subscription = new PushSubscription();
        $subscription->setUser($this->getUser());

        $form = $this->createForm(PushSubscribeType::class, $subscription);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new BadRequestHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($subscription);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * @Route("/unsubscribe", name="oaf_push_unsubscribe", options={"protect": true})
     */
    public function unsubscribeAction(Request $request)
    {
        $form = $this->createForm(PushUnsubscribeType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new BadRequestHttpException();
        }

        $endpoint = $form->getData()['endpoint'];
        $em = $this->getDoctrine()->getManager();
        $subscription = $em->getRepository(PushSubscription::class)->findOneByEndpoint($endpoint);
        if ($subscription && $subscription->getUser() === $this->getUser()) {
            $em->remove($subscription);
            $em->flush();
        }

        return new Response('', 204);
    }

    /**
     * @Template
     */
    public function settingsAction(Request $request)
    {
        return [
            'settings' => [
                'key' => $this->getParameter('vkaf_oaf.push.public_key'),
                'subscribe' => $this->generateUrl('oaf_push_subscribe'),
                'unsubscribe' => $this->generateUrl('oaf_push_unsubscribe'),
            ],
        ];
    }
}
