<?php

namespace Zentrium\Bundle\TimesheetBundle\Export;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Translation\TranslatorInterface;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\TimesheetBundle\Entity\EntryManager;

class Exporter
{
    /**
     * @var EntryManager
     */
    private $manager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(EntryManager $manager, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->translator = $translator;
    }

    public function export(ExportParameters $parameters)
    {
        $fp = fopen('php://temp', 'r+');

        $header = [
            $this->translator->trans('zentrium_timesheet.entry.field.id'),
            $this->translator->trans('zentrium_timesheet.entry.field.start'),
            $this->translator->trans('zentrium_timesheet.entry.field.end'),
            $this->translator->trans('zentrium_timesheet.entry.field.duration'),
            $this->translator->trans('zentrium.user.field.last_name'),
            $this->translator->trans('zentrium.user.field.first_name'),
            $this->translator->trans('zentrium_timesheet.entry.field.activity'),
            $this->translator->trans('zentrium_timesheet.entry.field.notes'),
            $this->translator->trans('zentrium_timesheet.entry.field.created'),
            $this->translator->trans('zentrium_timesheet.entry.field.author'),
            $this->translator->trans('zentrium_timesheet.entry.field.approved_at'),
            $this->translator->trans('zentrium_timesheet.entry.field.approved_by'),
        ];
        fputcsv($fp, $header);

        $entries = $this->fetchEntries($parameters);
        foreach ($entries as $entry) {
            $row = [
                $entry->getId(),
                $entry->getStart()->format('Y-m-d H:i:s'),
                $entry->getEnd()->format('Y-m-d H:i:s'),
                number_format($entry->getDuration() / 3600, 2, '.', ''), // log10(1/60) > -2
                $entry->getUser()->getLastName(),
                $entry->getUser()->getFirstName(),
                $entry->getActivity()->getName(),
                $entry->getNotes(),
                $entry->getCreated()->format('Y-m-d H:i:s'),
                $entry->getAuthor() !== null ? $entry->getAuthor()->getName(true) : '',
            ];
            if ($entry->isApproved()) {
                $row[] = $entry->getApprovedAt()->format('Y-m-d H:i:s');
                $row[] = $entry->getApprovedBy()->getName(true);
            }
            fputcsv($fp, $row);
        }

        rewind($fp);
        $content = stream_get_contents($fp);
        fclose($fp);

        $filename = $this->getFilename($parameters, 'csv');

        return $this->buildResponse($content, $filename, 'text/comma-separated-values');
    }

    protected function fetchEntries(ExportParameters $parameters)
    {
        $to = clone $parameters->getTo();
        $to = $to->setTime(23, 59, 59);

        $criteria = Criteria::create()
            ->where(Criteria::expr()->gte('start', $parameters->getFrom()))
            ->andWhere(Criteria::expr()->lte('start', $to))
            ->orderBy(['start' => Criteria::ASC])
        ;

        if ($parameters->getUserFilter() !== null) {
            $criteria->andWhere(Criteria::expr()->eq('user', $parameters->getUserFilter()));
        }

        if ($parameters->getGroupFilter() !== null) {
            $criteria->andWhere(Criteria::expr()->eq('groups', $parameters->getGroupFilter()));
        }

        return $this->manager->findByCriteria($criteria);
    }

    private function buildResponse($content, $filename, $mimeType)
    {
        $response = new Response($content);

        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename,
            iconv('UTF8', 'ASCII//TRANSLIT', $filename)
        ));

        return $response;
    }

    private function getFilename(ExportParameters $parameters, $extension)
    {
        $datePattern = $this->translator->trans('zentrium_timesheet.export.filename_date');
        $date = $parameters->getFrom()->format($datePattern);
        if ($parameters->getTo() != $parameters->getFrom()) {
            $date .= ' - '.$parameters->getTo()->format($datePattern);
        }

        $filename = $this->translator->trans('zentrium_timesheet.export.filename', [
            '%date%' => $date,
        ]);

        return $filename.'.'.$extension;
    }
}
