<?php 

namespace App\Helpers;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MessageListener implements MessageHandlerInterface{
    private EntityManagerInterface $em;
    private MessageHelper $message;

    public function __construct(EntityManagerInterface $em, MessageHelper $message) {
        $this->em = $em;
        $this->message = $message;
    }

    public function __invoke(MessageExecuted $message)
    {
        $user = $this->em->find(User::class, $message->getUserId());

        if($user) {
            $this->message->sendMessage($user);
        }
    }
}