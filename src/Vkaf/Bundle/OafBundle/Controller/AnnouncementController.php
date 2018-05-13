<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vkaf\Bundle\OafBundle\Announcement\AwsMessenger;

/**
 * @Route("/oaf/announcements")
 */
class AnnouncementController extends Controller
{
    /**
     * @Route("/messages/status")
     */
    public function statusAction(Request $request)
    {
        return $this->get(AwsMessenger::class)->handleStatusRequest($request);
    }
}
