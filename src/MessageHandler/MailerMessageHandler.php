<?php

namespace App\MessageHandler;

use App\Message\MailerMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class MailerMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private MailerInterface $mailer
    )
    {
    }

    public function __invoke(MailerMessage $message): void
    {
        $this->mailer->send($message->getEmail());
    }
}
