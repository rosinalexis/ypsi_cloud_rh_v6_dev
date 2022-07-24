<?php

namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        //if ($_ENV['APP_ENV'] == 'dev') return;

        $exception = $event->getThrowable();

        if($exception instanceof HttpExceptionInterface){
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];

        }else{
            $data = [
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage()
            ];
        }
        $event->setResponse(new JsonResponse($data));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
