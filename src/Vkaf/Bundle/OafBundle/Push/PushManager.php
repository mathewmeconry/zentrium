<?php

namespace Vkaf\Bundle\OafBundle\Push;

use Doctrine\ORM\EntityManagerInterface;
use Minishlink\WebPush\WebPush;
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

    public function __construct(WebPush $push, EntityManagerInterface $em)
    {
        $this->push = $push;
        $this->em = $em;
    }

    public function send(User $user, $topic, $title, $body = null, $url = null)
    {
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
        $this->em->transactional(function (EntityManagerInterface $em) use ($subscriptions, $results) {
            foreach ($subscriptions as $i => $subscription) {
                if ($results === true || $results[$i]['success']) {
                    $subscription->refresh();
                } else {
                    $this->em->remove($subscription);
                }
            }
        });
    }
}