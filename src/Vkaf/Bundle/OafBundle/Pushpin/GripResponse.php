<?php

namespace Vkaf\Bundle\OafBundle\Pushpin;

use GripControl\GripControl;
use GripControl\WebSocketEvent;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GripResponse extends StreamedResponse
{
    private $events;

    public function __construct(array $events = [], $status = 200, array $headers = [])
    {
        parent::__construct(function () {
            return $this->sendEvents();
        }, $status, $headers + [
            'Content-Type' => 'application/websocket-events',
            'Sec-WebSocket-Extensions' => 'grip; message-prefix=""',
        ]);

        $this->events = $events;
    }

    public function addEvent(WebSocketEvent $event)
    {
        $this->events[] = $event;
    }

    private function sendEvents()
    {
        echo GripControl::encode_websocket_events($this->events);
    }
}
