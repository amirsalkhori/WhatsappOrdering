<?php


namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Bridge\UserHashInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashPassword implements EventSubscriberInterface
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['hashPass', EventPriorities::PRE_WRITE],
        ];
    }

    public function hashPass(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($user instanceof UserHashInterface && in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])) {
            if ($this->userPasswordHasher->needsRehash($user))
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
        }
    }
}