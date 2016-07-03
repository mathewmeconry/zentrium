<?php

namespace Zentrium\Bundle\TimesheetBundle\Controller;

use DateTime;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;
use Zentrium\Bundle\TimesheetBundle\Form\Type\EntryApprovalType;

/**
 * @NamePrefix("api_timesheet_entries")
 */
class EntryApiController extends FOSRestController
{
    /**
     * @Get("/api/timesheet/entries")
     * @View(serializerGroups={"Default", "list"})
     */
    public function cgetAction()
    {
        $entries = $this->get('zentrium_timesheet.manager.entry')->findAll();

        return $entries;
    }

    /**
     * @Get("/api/timesheet/entries/{entry}")
     * @View(serializerGroups={"Default", "details"})
     */
    public function getAction(Entry $entry)
    {
        return $entry;
    }

    /**
     * @Post("/api/timesheet/entries/{entry}/approval")
     * @View
     */
    public function approveAction(Request $request, Entry $entry)
    {
        if ($entry->isApproved()) {
            throw new BadRequestHttpException('This entry has already been approved.');
        }

        $entry->setApprovedAt(new DateTime());

        $form = $this->createForm(EntryApprovalType::class, $entry, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);
        if (!$form->isValid()) {
            throw new BadRequestHttpException();
        }

        $manager = $this->get('zentrium_timesheet.manager.entry');
        $manager->save($entry);

        return $entry;
    }
}
