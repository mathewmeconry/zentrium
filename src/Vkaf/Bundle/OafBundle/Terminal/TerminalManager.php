<?php

namespace Vkaf\Bundle\OafBundle\Terminal;

use Doctrine\ORM\EntityManager;
use Firebase\JWT\JWT;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Vkaf\Bundle\OafBundle\Entity\Terminal;
use Vkaf\Bundle\OafBundle\Pushpin\CloseEvent;
use Vkaf\Bundle\OafBundle\Pushpin\GripPublisher;
use Vkaf\Bundle\OafBundle\Pushpin\GripRequest;
use Vkaf\Bundle\OafBundle\Pushpin\GripResponse;
use Vkaf\Bundle\OafBundle\Pushpin\MessageEvent;
use Vkaf\Bundle\OafBundle\Pushpin\OpenEvent;
use Vkaf\Bundle\OafBundle\Pushpin\SubscribeEvent;

class TerminalManager
{
    const FLOW_EVENT = 'vkaf_oaf.terminal.flow';
    const FLOW_TIMEOUT = 5 * 60;
    const FLOW_TOKEN = 'vkaf_oaf.terminal.flow';

    private $grip;
    private $em;
    private $dispatcher;
    private $tokenKey;

    public function __construct(GripPublisher $grip, EntityManager $em, EventDispatcherInterface $dispatcher, string $tokenKey)
    {
        $this->grip = $grip;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->tokenKey = $tokenKey;
    }

    public function start(Terminal $terminal, string $flow, $params, string $tag, array $context = []): string
    {
        $token = JWT::encode([
            'iss' => self::FLOW_TOKEN,
            'aud' => $terminal->getId(),
            'sub' => $tag,
            'iat' => time(),
            'exp' => time() + self::FLOW_TIMEOUT,
            'context' => $context,
        ], $this->tokenKey, 'HS256');

        $this->grip->publish('terminal:'.$terminal->getId(), [
            'flow' => [
                'name' => $flow,
                'token' => $token,
                'params' => $params,
            ],
        ]);

        return $token;
    }

    public function handle(Terminal $terminal, GripRequest $request): GripResponse
    {
        $response = new GripResponse();
        foreach ($request->getEvents() as $event) {
            if ($event instanceof OpenEvent) {
                $this->update($terminal, true);
                $response->addEvent(new OpenEvent());
                $response->addEvent(new SubscribeEvent('terminal:'.$terminal->getId()));
            } elseif ($event instanceof MessageEvent) {
                $message = $event->getMessage();
                if (isset($message['flow']) && is_array($message['flow'])) {
                    $this->handleFlowMessage($terminal, $message['flow']);
                }
            } elseif ($event instanceof CloseEvent) {
                $this->update($terminal, false);
            }
        }

        return $response;
    }

    private function handleFlowMessage(Terminal $terminal, array $message)
    {
        if (!isset($message['token']) || !is_string($message['token'])) {
            throw new RuntimeException();
        }
        $token = (array) JWT::decode($message['token'], $this->tokenKey, ['HS256']);
        if ($token['iss'] !== self::FLOW_TOKEN || (int) $token['aud'] !== $terminal->getId()) {
            throw new RuntimeException();
        }

        $this->dispatcher->dispatch(self::FLOW_EVENT, new FlowEvent($token['sub'], $message['data'] ?? null, (array) $token['context']));
    }

    public function getStatusChannel()
    {
        return 'status';
    }

    public function renderStatus()
    {
        $terminals = $this->em->getRepository(Terminal::class)->findAll();

        return [
            'terminals' => array_map(function (Terminal $terminal) {
                return [
                    'id' => $terminal->getId(),
                    'label' => $terminal->getLabel(),
                    'online' => $terminal->isOnline(),
                ];
            }, $terminals),
        ];
    }

    private function update(Terminal $terminal, $online)
    {
        $terminal->setOnline($online);
        $this->em->flush($terminal);
        $this->grip->publish($this->getStatusChannel(), $this->renderStatus());
    }
}
