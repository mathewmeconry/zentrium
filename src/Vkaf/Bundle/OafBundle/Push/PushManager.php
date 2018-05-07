<?php

namespace Vkaf\Bundle\OafBundle\Push;

use Doctrine\ORM\EntityManagerInterface;
use Minishlink\WebPush\WebPush;
use Psr\Log\LoggerInterface;
use Vkaf\Bundle\OafBundle\Entity\PushSubscription;
use Zentrium\Bundle\CoreBundle\Entity\User;

class PushManager
{
    /**
     * @var WebPush
     */
    private $push;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(WebPush $push, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->push = $push;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function send(User $user, $topic, $title, $body = null, $url = null)
    {
        $this->logger->info(sprintf('Sending push notification to %s', $user->getName()), ['user' => $user->getId(), 'topic' => $topic, 'title' => $title]);

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'tag' => $topic,
        ]);
        $subscriptions = $this->em->getRepository(PushSubscription::class)->findByUser($user);

        foreach ($subscriptions as $subscription) {
            $this->push->sendNotification(
                $subscription->getEndpoint(),
                $payload,
                $subscription->getKey(),
                $subscription->getToken(),
                false,
                ['topic' => $topic]
            );
        }

        $results = $this->push->flush();
        $logger = $this->logger;
        $this->em->transactional(function (EntityManagerInterface $em) use ($user, $subscriptions, $results, $logger) {
            foreach ($subscriptions as $i => $subscription) {
                if ($results === true || $results[$i]['success']) {
                    $subscription->refresh();
                } else {
                    $logger->notice('Failed to send push notification', ['user' => $user->getId(), 'message' => $results[$i]['message'] ?? null]);
                    $this->em->remove($subscription);
                }
            }
        });
    }
}
