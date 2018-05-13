<?php

namespace Vkaf\Bundle\OafBundle\Announcement;

use Aws\Sns\Exception\InvalidSnsMessageException;
use Aws\Sns\Message as SnsMessage;
use Aws\Sns\MessageValidator;
use Aws\Sns\SnsClient;
use Doctrine\ORM\EntityManagerInterface;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Vkaf\Bundle\OafBundle\Entity\Message;
use Vkaf\Bundle\OafBundle\Entity\MessageDelivery;

class AwsMessenger implements MessengerInterface
{
    private $sns;
    private $validator;
    private $em;
    private $senderId;
    private $sendTopic;
    private $statusTopic;
    private $phoneNumberUtil;
    private $logger;

    public function __construct(SnsClient $sns, MessageValidator $validator, EntityManagerInterface $em, string $senderId, string $sendTopic, string $statusTopic, PhoneNumberUtil $phoneNumberUtil, LoggerInterface $logger)
    {
        $this->sns = $sns;
        $this->validator = $validator;
        $this->em = $em;
        $this->senderId = $senderId;
        $this->sendTopic = $sendTopic;
        $this->statusTopic = $statusTopic;
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->logger = $logger;
    }

    public function send(array $receivers, string $text)
    {
        $numbers = [];
        foreach ($receivers as $receiver) {
            if ($receiver->getMobilePhone() === null) {
                continue;
            }
            $number = $this->phoneNumberUtil->format($receiver->getMobilePhone(), PhoneNumberFormat::E164);
            if (!isset($numbers[$number])) {
                $numbers[$number] = [];
            }
            $numbers[$number][] = $receiver;
        }

        $result = $this->sns->publish([
            'TopicArn' => $this->sendTopic,
            'Message' => $text,
            'MessageAttributes' => [
                'AWS.SNS.SMS.SenderID' => [
                    'DataType' => 'String',
                    'StringValue' => $this->senderId,
                ],
                'PhoneNumbers' => [
                    'DataType' => 'String.Array',
                    'StringValue' => json_encode(array_keys($numbers)),
                ],
                'StatusTopic' => [
                    'DataType' => 'String',
                    'StringValue' => $this->statusTopic,
                ],
            ],
        ]);

        $message = new Message();
        $message->setText($text);
        foreach ($numbers as $number => $users) {
            foreach ($users as $user) {
                $delivery = new MessageDelivery($message, $user, $user->getMobilePhone());
                $delivery->setExtra($result['MessageId']);
                $message->getDeliveries()->add($delivery);
            }
        }
        $this->em->transactional(function (EntityManagerInterface $em) use ($message) {
            $em->persist($message);
        });

        return $message;
    }

    public function handleStatusRequest(Request $request)
    {
        $requestBody = json_decode($request->getContent(), true);
        if (!is_array($requestBody)) {
            throw new BadRequestHttpException();
        }
        $message = new SnsMessage($requestBody);
        try {
            $this->validator->validate($message);
        } catch (InvalidSnsMessageException $exception) {
            throw new BadRequestHttpException(null, $exception);
        }
        if ($message['TopicArn'] !== $this->statusTopic) {
            throw new BadRequestHttpException();
        }

        switch ($message['Type']) {
            case 'Notification':
                $status = json_decode($message['Message'], true);
                if (!is_array($status)) {
                    throw new BadRequestHttpException();
                }
                $this->handleStatus($status);
                break;
            case 'SubscriptionConfirmation':
                $this->logger->notice('Confirm subscription', ['url' => $message['SubscribeURL']]);
                file_get_contents($message['SubscribeURL']);
                break;
        }

        return new Response('', 204);
    }

    private function handleStatus(array $status)
    {
        $id = $status['notification']['messageId'];
        $number = $this->phoneNumberUtil->parse($status['delivery']['destination']);
        if ($status['status'] === 'SUCCESS') {
            $this->logger->info(sprintf('Delivered message to %s: %s', $status['delivery']['destination'], $status['delivery']['providerResponse'] ?? ''), ['message' => $id]);
            $status = true;
        } else {
            $this->logger->error(sprintf('Could not deliver message to %s: %s', $status['delivery']['destination'], $status['delivery']['providerResponse'] ?? ''), ['message' => $id]);
            $status = false;
        }

        $this->em->transactional(function (EntityManagerInterface $em) use ($id, $number, $status) {
            $deliveries = $this->em->getRepository(MessageDelivery::class)->findBy(['number' => $number, 'extra' => $id]);
            foreach ($deliveries as $delivery) {
                $delivery->setStatus($status);
                $delivery->update();
            }
        });
    }
}
