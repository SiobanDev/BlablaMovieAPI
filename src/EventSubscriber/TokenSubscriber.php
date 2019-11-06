<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

//This class has been created in order to add a header to each request.
//Instead of writing it in each function of each Controller, or creating a DefaultController, I create a general event on the API, which had a header to each call of the API.
class TokenSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();

        // create a hash and set it as a response header
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-type');

    }

}