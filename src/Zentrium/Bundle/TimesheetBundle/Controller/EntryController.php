<?php

namespace Zentrium\Bundle\TimesheetBundle\Controller;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;
use Zentrium\Bundle\TimesheetBundle\Event\EntryEvents;
use Zentrium\Bundle\TimesheetBundle\Event\GetResponseEntryEvent;
use Zentrium\Bundle\TimesheetBundle\Export\ExportParameters;
use Zentrium\Bundle\TimesheetBundle\Form\Type\EntryType;
use Zentrium\Bundle\TimesheetBundle\Form\Type\ExportParametersType;

/**
 * @Route("/timesheet")
 */
class EntryController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="timesheet_entries")
     * @Template
     */
    public function indexAction()
    {
        $entries = $this->get('zentrium_timesheet.manager.entry')->findAll();

        return [
            'entries' => $entries,
        ];
    }

    /**
     * @Route("/export", name="timesheet_entries_export")
     * @Template
     */
    public function exportAction(Request $request)
    {
        $parameters = new ExportParameters();

        $form = $this->createForm(ExportParametersType::class, $parameters);

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->get('zentrium_timesheet.export')->export($parameters);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new", name="timesheet_entry_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        $entry = new Entry();
        $entry->setStart(new DateTime());
        $entry->setEnd(new DateTime());

        return $this->handleEdit($request, $entry);
    }

    /**
     * @Route("/{entry}/edit", name="timesheet_entry_edit")
     * @Template
     */
    public function editAction(Request $request, Entry $entry)
    {
        return $this->handleEdit($request, $entry);
    }

    private function handleEdit(Request $request, Entry $entry)
    {
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseEntryEvent($entry, $request);
        $dispatcher->dispatch(EntryEvents::EDIT_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('zentrium_timesheet.manager.entry');
            $manager->save($entry);

            $event = new GetResponseEntryEvent($entry, $request);
            $dispatcher->dispatch(EntryEvents::EDIT_COMPLETED, $event);
            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $this->addFlash('success', 'zentrium_timesheet.entry.form.saved');

            return $this->redirectToRoute('timesheet_entries');
        }

        return [
            'entry' => $entry,
            'form' => $form->createView(),
        ];
    }
}
