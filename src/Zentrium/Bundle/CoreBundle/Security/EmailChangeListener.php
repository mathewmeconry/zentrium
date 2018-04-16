<?php

namespace Zentrium\Bundle\CoreBundle\Security;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Zentrium\Bundle\CoreBundle\Entity\User;

class EmailChangeListener
{
    public function preUpdate(User $user, PreUpdateEventArgs $event)
    {
        if (!$event->hasChangedField('emailCanonical')) {
            return;
        }

        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
    }
}
