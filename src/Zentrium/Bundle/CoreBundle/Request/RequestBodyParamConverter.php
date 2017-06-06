<?php

namespace Zentrium\Bundle\CoreBundle\Request;

use FOS\RestBundle\Request\RequestBodyParamConverter as BaseRequestBodyParamConverter;
use FOS\RestBundle\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zentrium\Bundle\CoreBundle\Validator\ValidationException;

class RequestBodyParamConverter extends BaseRequestBodyParamConverter
{
    private $validationErrorsArgument;

    public function __construct(Serializer $serializer, $groups = null, $version = null, ValidatorInterface $validator = null, $validationErrorsArgument = null)
    {
        parent::__construct($serializer, $groups, $version, $validator, $validationErrorsArgument);

        $this->validationErrorsArgument = $validationErrorsArgument;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $success = parent::apply($request, $configuration);

        if (!$success || $this->validationErrorsArgument === null) {
            return $success;
        }

        $options = (array) $configuration->getOptions();
        if (isset($options['rejectInvalid']) && !$options['rejectInvalid']) {
            return true;
        }

        if (($errors = $request->attributes->get($this->validationErrorsArgument)) === null) {
            return true;
        }

        if (count($errors)) {
            throw new ValidationException($errors);
        }

        $request->attributes->remove($this->validationErrorsArgument);

        return true;
    }
}
