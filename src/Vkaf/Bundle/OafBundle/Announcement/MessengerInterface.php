<?php

namespace Vkaf\Bundle\OafBundle\Announcement;

use Zentrium\Bundle\CoreBundle\Entity\User;

interface MessengerInterface
{
    public function send(array $receivers, string $message, User $sender = null);
}
