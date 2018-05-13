<?php

namespace Vkaf\Bundle\OafBundle\Announcement;

interface MessengerInterface
{
    public function send(array $receivers, string $message);
}
