<?php

namespace Zentrium\Bundle\LogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\LogBundle\Entity\Comment;
use Zentrium\Bundle\LogBundle\Entity\Log;
use Zentrium\Bundle\LogBundle\Form\Type\CommentType;
use Zentrium\Bundle\LogBundle\Form\Type\LogType;

class LogController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/logs", name="logs")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $activeStatus = $request->query->get('status', Log::STATUS_OPEN);
        if (!in_array($activeStatus, Log::getStatuses())) {
            throw $this->createNotFoundException('Unknown status.');
        }

        $activeLabels = array_unique(array_filter(array_map('intval', explode(',', $request->query->get('labels')))));

        $logRepository = $this->getDoctrine()->getRepository('ZentriumLogBundle:Log');
        $logs = $logRepository->findByStatusWithLabels($activeStatus, $activeLabels);
        $statusCounts = $logRepository->aggregateByStatus();
        $commentCounts = $logRepository->countComments();

        $labels = $this->getDoctrine()->getRepository('ZentriumLogBundle:Label')->findAll();

        return [
            'logs' => $logs,
            'activeStatus' => $activeStatus,
            'activeLabels' => $activeLabels,
            'statuses' => Log::getStatuses(),
            'statusCounts' => $statusCounts,
            'commentCounts' => $commentCounts,
            'labels' => $labels,
        ];
    }

    /**
     * @Route("/logs/new", name="log_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        $log = new Log();
        $log->setAuthor($this->getUser());

        return $this->handleEdit($request, $log);
    }

    /**
     * @Route("/logs/{log}", name="log_view")
     * @Template
     */
    public function viewAction(Request $request, Log $log)
    {
        $commentForm = $this->createForm(CommentType::class, new Comment());

        return [
            'log' => $log,
            'commentForm' => $commentForm->createView(),
        ];
    }

    /**
     * @Route("/logs/{log}/edit", name="log_edit")
     * @Template
     */
    public function editAction(Request $request, Log $log)
    {
        return $this->handleEdit($request, $log);
    }

    /**
     * @Route("/logs/{log}/comments/new", name="log_comment_new")
     * @Template
     */
    public function newCommentAction(Request $request, Log $log)
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setLog($log);
        $log->triggerUpdate();
        $log->getComments()->add($comment);

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($log);
            $em->flush();

            $this->addFlash('success', 'zentrium_log.comment.form.saved');
        }

        return $this->redirectToRoute('log_view', ['log' => $log->getId()]);
    }

    private function handleEdit(Request $request, Log $log)
    {
        $form = $this->createForm(LogType::class, $log);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($log);
            $em->flush();

            $this->addFlash('success', 'zentrium_log.log.form.saved');

            return $this->redirectToRoute('log_view', ['log' => $log->getId()]);
        }

        return [
            'log' => $log,
            'form' => $form->createView(),
        ];
    }
}
