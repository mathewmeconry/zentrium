<?php

namespace Vkaf\Bundle\OafBundle\Announcement;

use Symfony\Component\Validator\Constraints as Assert;
use Vkaf\Bundle\OafBundle\Validator\Constraints as AssertOaf;

class MessageDraft
{
    private $receivers;

    /**
     * @Assert\NotBlank
     * @AssertOaf\SmsCount(max=2)
     */
    private $text;

    public function getReceivers()
    {
        return $this->receivers;
    }

    public function setReceivers($receivers)
    {
        $this->receivers = $receivers;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }
}
