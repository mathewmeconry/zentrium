<?php

namespace Zentrium\Bundle\CoreBundle\Request;

use FOS\RestBundle\Request\RequestBodyParamConverter as BaseRequestBodyParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Validator\ValidationException;

class RequestBodyParamConverter extends BaseRequestBodyParamConverter
{
    protected function execute(Request $request, ParamConverter $configuration)
    {
        parent::execute($request, $configuration);

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
