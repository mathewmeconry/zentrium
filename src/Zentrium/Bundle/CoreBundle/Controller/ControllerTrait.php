<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

trait ControllerTrait
{
    protected function addFlash($type, $messageId, $messageParameters = [])
    {
        $message = $this->container->get('translator')->trans($messageId, $messageParameters);

        $this->container->get('session')->getFlashBag()->add($type, $message);
    }
}
