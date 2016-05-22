<?php

namespace Zentrium\Bundle\CoreBundle\Validator;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends BadRequestHttpException
{
    /**
     * @var ConstraintValidationListInterface
     */
    private $errors;

    public function __construct(ConstraintViolationListInterface $errors, $message = null, $code = 0)
    {
        parent::__construct(($message !== null ? $message : 'Validation failed'), null, $code);

        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
