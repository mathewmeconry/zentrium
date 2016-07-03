<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vkaf\Bundle\OafBundle\Entity\Kiosk;
use Vkaf\Bundle\OafBundle\Kiosk\Slide\RenderException;

/**
 * @Route("/kiosk")
 */
class KioskController extends Controller
{
    /**
     * @Route("", name="kiosk")
     */
    public function slideAction(Request $request)
    {
        $kiosk = $this->getUser();
        if (!($kiosk instanceof Kiosk)) {
            throw $this->createNotFoundException();
        }

        if ($kiosk->getSlides()->isEmpty()) {
            return $this->renderMessage('vkaf_oaf.kiosk.no_slides', null);
        }

        $slideIndex = max(0, intval($request->query->get('slide')));
        $slideIndex %= count($kiosk->getSlides());
        $slide = $kiosk->getSlides()->get($slideIndex);

        $nextUrl = $this->generateUrl('kiosk', ['token' => $kiosk->getToken(), 'slide' => ($slideIndex + 1)]);
        $next = [
            'duration' => $slide->getDuration(),
            'next' => $nextUrl,
        ];

        if ($slide->isHidden()) {
            return $this->redirect($nextUrl);
        }

        try {
            return $this->get('vkaf_oaf.kiosk.slide_manager')->render($slide->getType(), $slide->getOptions(), $next);
        } catch (RenderException $e) {
            return $this->renderMessage('vkaf_oaf.kiosk.exception', $nextUrl);
        }
    }

    private function renderMessage($message, $nextUrl)
    {
        return $this->render('VkafOafBundle:Kiosk:message.html.twig', [
            'message' => $message,
            'next' => [
                'duration' => 10,
                'next' => $nextUrl,
            ],
        ]);
    }
}
