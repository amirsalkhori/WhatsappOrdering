<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Bridge\CreatedAt;
use App\Bridge\UpdatedAt;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CreatedAtSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [KernelEvents::VIEW => ['createdAt', EventPriorities::PRE_WRITE],];
    }

    public function createdAt(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($entity instanceof CreatedAt && Request::METHOD_POST === $method)
        {
            $entity->setCreatedAt(new \DateTime());
        }

        if ($entity instanceof UpdatedAt && (Request::METHOD_PUT === $method || Request::METHOD_POST === $method))
        {
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}
