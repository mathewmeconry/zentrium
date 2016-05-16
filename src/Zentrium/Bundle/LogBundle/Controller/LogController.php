<?php

namespace Zentrium\Bundle\LogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\LogBundle\Entity\Log;
use Zentrium\Bundle\LogBundle\Form\Type\LogType;

class LogController extends Controller
{
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

        $labels = $this->getDoctrine()->getRepository('ZentriumLogBundle:Label')->findAll();

        return [
            'logs' => $logs,
            'activeStatus' => $activeStatus,
            'activeLabels' => $activeLabels,
            'statuses' => Log::getStatuses(),
            'statusCounts' => $statusCounts,
            'labels' => $labels,
        ];
    }

    /**
     * @Route("/logs/new", name="log_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new Log());
    }

    /**
     * @Route("/logs/{log}", name="log_view")
     * @Template
     */
    public function viewAction(Request $request, Log $log)
    {
        return [
            'log' => $log,
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
