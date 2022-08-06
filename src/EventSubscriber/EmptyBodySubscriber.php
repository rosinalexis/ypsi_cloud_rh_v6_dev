<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Exception\EmptyBodyException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class EmptyBodySubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['handleEmptyBody',EventPriorities::POST_DESERIALIZE]
        ];
    }


    public function handleEmptyBody(RequestEvent $event)
    {
        $method =$event->getRequest()->getMethod();

        if(!in_array($method,[Request::METHOD_POST,Request::METHOD_PUT]))
        {
            return;
        }

        $data = $event->getRequest()->get('data');

        $objectBaseClass = $data::class;
        $newInstanceOfBaseClasse = new $objectBaseClass();

        if($data == $newInstanceOfBaseClasse)
        {
            throw new EmptyBodyException();
        }
    }
}