<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Vkaf\Bundle\OafBundle\Announcement\AwsMessenger;
use Vkaf\Bundle\OafBundle\Announcement\CostEstimator;
use Vkaf\Bundle\OafBundle\Announcement\MessageDraft;
use Vkaf\Bundle\OafBundle\Announcement\MessengerInterface;
use Vkaf\Bundle\OafBundle\Entity\Message;
use Vkaf\Bundle\OafBundle\Form\Type\MessageDraftType;
use Zentrium\Bundle\CoreBundle\Entity\User;

/**
 * @Route("/oaf/announcements")
 */
class AnnouncementController extends Controller
{
    const CONFIRM_NS = 'oaf_messages/';

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
     * @Route("/messages/send", name="oaf_message_send")
     * @Template
     */
    public function messageSendAction(Request $request)
    {
        $draft = new MessageDraft();
        $form = $this->createForm(MessageDraftType::class, $draft);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = bin2hex(random_bytes(10));
            $request->getSession()->set(self::CONFIRM_NS.$token, $draft);

            return $this->redirectToRoute('oaf_message_confirm', ['token' => $token]);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/messages/send/{token}", name="oaf_message_confirm")
     * @Template
     */
    public function messageConfirmAction(Request $request, $token)
    {
        $draft = $request->getSession()->get(self::CONFIRM_NS.$token);
        if (!$draft) {
            return $this->redirectToRoute('oaf_message_send');
        }

        $userRepository = $this->get('zentrium.repository.user');
        $draft->setReceivers($draft->getReceivers()->map(function (User $user) use ($userRepository) {
            return $userRepository->find($user->getId());
        }));

        $form = $this->createFormBuilder()
            ->add('confirm', SubmitType::class, ['label' => 'vkaf_oaf.message.form.send'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->remove(self::CONFIRM_NS.$token);

            $this->get(MessengerInterface::class)->send($draft->getReceivers()->toArray(), $draft->getText(), $this->getUser());

            return $this->redirectToRoute('oaf_messages');
        }

        $receivers = array_filter($draft->getReceivers()->map(function (User $user) {
            return $user->getMobilePhone();
        })->toArray());
        $cost = $this->get(CostEstimator::class)->estimate($receivers, $draft->getText());

        return [
            'form' => $form->createView(),
            'draft' => $draft,
            'cost' => $cost,
        ];
    }

    /**
     * @Route("/messages/status")
     */
    public function messageStatusAction(Request $request)
    {
        return $this->get(AwsMessenger::class)->handleStatusRequest($request);
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
}
