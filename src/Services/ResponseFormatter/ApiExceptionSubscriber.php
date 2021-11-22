<?php

namespace App\Services\ResponseFormatter;

use App\Services\DtoBuilder\InvalidDTOException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ApiExceptionSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'handle',
        ];
    }

    public function handle(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if(!$exception instanceof InvalidDTOException) {
            return;
        }

        $response = [
            'data' => [],
            'errors' => [],
        ];
        $statusCode = 400;

        if($exception instanceof InvalidDTOException) {
            $response['errors'] = $this->getErrorMessages($exception->getViolations());
        }

        $event->setResponse(new JsonResponse($response, $statusCode));
    }

    private function getErrorMessages(ConstraintViolationListInterface $constraints): array
    {
        $errors = [];
        /** @var ConstraintViolation $constraint */
        foreach ($constraints as $constraint) {
            $errors[$constraint->getPropertyPath()] = $constraint->getMessage();
        }

        return $errors;
    }
}