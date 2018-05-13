<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vkaf\Bundle\OafBundle\Announcement\AwsMessenger;
use Vkaf\Bundle\OafBundle\Entity\Message;
use Zentrium\Bundle\CoreBundle\Entity\User;

/**
 * @Route("/oaf/announcements")
 */
class AnnouncementController extends Controller
{
    /**
     * @Route("/", name="oaf_announcements")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('oaf_messages');
    }

    /**
     * @Route("/messages", name="oaf_messages")
     * @Template
     */
    public function messagesAction()
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->findAll();

        return [
            'messages' => $messages,
        ];
    }

    /**
     * @Route("/messages/user/{user}", name="oaf_messages_user")
     * @Template
     */
    public function messagesUserAction(User $user)
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->findByUser($user);

        return [
            'user' => $user,
            'messages' => $messages,
        ];
    }

    /**
     * @Route("/messages/{message}", name="oaf_message")
     * @ParamConverter("message", options={"repository_method"="findWithUsers"})
     * @Template
     */
    public function messageAction(Message $message)
    {
        return [
            'message' => $message,
        ];
    }

    /**
     * @Route("/messages/status")
     */
    public function messageStatusAction(Request $request)
    {
        return $this->get(AwsMessenger::class)->handleStatusRequest($request);
    }
}
