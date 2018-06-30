<?php

namespace Vkaf\Bundle\OafBundle\Pushpin;

use GripControl\GripControl;
use GripControl\WebSocketEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GripParamConverter implements ParamConverterInterface
{
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === GripRequest::class;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        if ($request->headers->get('Content-Type') !== 'application/websocket-events') {
            throw new BadRequestHttpException();
        }

        if (!GripControl::validate_sig($request->headers->get('Grip-Sig'), $this->key)) {
            throw new AccessDeniedHttpException();
        }

        $events = [];
        foreach (GripControl::decode_websocket_events($request->getContent()) as $event) {
            $events[] = $this->mapEvent($event);
        }
        $request->attributes->set($configuration->getName(), new GripRequest($events));

        return true;
    }

    protected function mapEvent(WebSocketEvent $event)
    {
        switch ($event->type) {
            case 'OPEN':
                return new OpenEvent();
            case 'TEXT':
                $message = json_decode($event->content, true);
                if (is_array($message)) {
                    return new MessageEvent($message);
                }
                break;
            case 'DISCONNECT':
                return new CloseEvent(false);
            case 'CLOSE':
                return new CloseEvent(strlen($event->content) === 2 ? unpack('n', $event->content)[1] : 0);
        }

        return $event;
    }
}
