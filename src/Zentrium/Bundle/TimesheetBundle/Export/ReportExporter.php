<?php

namespace Zentrium\Bundle\TimesheetBundle\Export;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\TimesheetBundle\Entity\EntryManager;

class ReportExporter extends Exporter
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct(EntryManager $manager, TranslatorInterface $translator, Twig_Environment $twig)
    {
        parent::__construct($manager, $translator);

        $this->translator = $translator;
        $this->twig = $twig;
    }

    public function export(ExportParameters $parameters)
    {
        $entries = $this->fetchEntries($parameters);

        $rows = [];
        $duration = 0;
        foreach ($entries as $entry) {
            $userId = $entry->getUser()->getId();
            if (!isset($rows[$userId])) {
                $rows[$userId] = [
                    'user' => $entry->getUser(),
                    'activities' => [],
                    'entries' => [],
                    'duration' => 0,
                ];
            }

            $activityId = $entry->getActivity()->getId();
            if (!isset($rows[$userId]['activities'][$activityId])) {
                $rows[$userId]['activities'][$activityId] = [
                    'activity' => $entry->getActivity(),
                    'duration' => 0,
                ];
            }

            $rows[$userId]['entries'][] = $entry;

            $rows[$userId]['activities'][$activityId]['duration'] += $entry->getDuration();
            $rows[$userId]['duration'] += $entry->getDuration();
            $duration += $entry->getDuration();
        }

        uasort($rows, function ($a, $b) {
            return strcasecmp($a['user']->getName(true), $b['user']->getName(true));
        });

        return new Response($this->twig->render('ZentriumTimesheetBundle:Export:report.html.twig', [
            'parameters' => $parameters,
            'rows' => $rows,
            'duration' => $duration,
        ]));
    }
}
