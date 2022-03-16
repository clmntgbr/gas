<?php

namespace App\Service;

use App\Message\MailerMessage;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private MessageBusInterface $messageBus
    )
    {
    }

    public function send(Email $email): void
    {
        $this->messageBus->dispatch(new MailerMessage(
            $email
        ), [new AmqpStamp('async-priority-high', AMQP_NOPARAM, [])]);
    }
}
