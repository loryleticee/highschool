<?php

namespace App\Helpers;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MessageHelper
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMessage(User $user)
    {
        $message = new Email();
        $message->from('sandbox@loryleticee.com')->to($user->getEmail())->html("<span>OK ici le contenue de mon mail<span>");

        $this->mailer->send($message);
    }
}
