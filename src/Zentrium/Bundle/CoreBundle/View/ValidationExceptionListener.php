<?php

namespace Zentrium\Bundle\CoreBundle\View;

use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Zentrium\Bundle\CoreBundle\Validator\ValidationException;

class ValidationExceptionListener
{
    private $exceptionWrapper;
    private $viewHandler;

    public function __construct(ExceptionWrapperHandlerInterface $exceptionWrapper, ViewHandlerInterface $viewHandler)
    {
        $this->exceptionWrapper = $exceptionWrapper;
        $this->viewHandler = $viewHandler;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!($exception instanceof ValidationException)) {
            return;
        }

        if ($this->viewHandler->isFormatTemplating($event->getRequest()->getRequestFormat())) {
            return;
        }

        $parameters = $this->exceptionWrapper->wrap([
            'status_code' => $exception->getStatusCode(),
            'message' => 'Validation failed',
            'errors' => $exception->getErrors(),
        ]);

        $view = View::create($parameters, $exception->getStatusCode(), $exception->getHeaders());
        $event->setResponse($this->viewHandler->handle($view));
    }
}
