<?php

namespace Vkaf\Bundle\OafBundle\Announcement;

use Instasent\SMSCounter\SMSCounter;

class CostEstimator
{
    private $path;
    private $data;
    private $counter;

    public function __construct(string $path, SMSCounter $counter)
    {
        $this->path = $path;
        $this->counter = $counter;
    }

    public function estimate($receivers, $text)
    {
        if (!$this->data) {
            $this->data = json_decode(file_get_contents($this->path), true);
        }

        $count = $this->counter->count($text)->messages;
        $cost = 0.0;
        foreach ($receivers as $receiver) {
            $country = (string) $receiver->getCountryCode();
            if ($country && isset($this->data[$country])) {
                $cost += $this->data[$country] * $count;
            }
        }

        return $cost;
    }
}
