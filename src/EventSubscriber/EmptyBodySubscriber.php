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
        $request = $event->getRequest();
        $method =  $request->getMethod();
        $route = $request->get('_route');

        if(
            !in_array($method,[Request::METHOD_POST,Request::METHOD_PUT]) ||
            in_array($request->getContentType(),['html','form']) ||
            !str_starts_with($route, 'api'))
        {
            return;
        }

        //dd($request->getContentType());
        // vérification si l'objet json est vide ou null
        $data = $event->getRequest()->get('data');
        $objectBaseClass = $data::class;
        $newInstanceOfBaseClasse = new $objectBaseClass();

        if($data == $newInstanceOfBaseClasse)
        {
            throw new EmptyBodyException();
        }
    }
}