<?php

namespace Zentrium\Bundle\CoreBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\JsonSerializationVisitor;

class ValidationExceptionHandler
{
    public function serialize(JsonSerializationVisitor $visitor, $exception, array $type, Context $context)
    {
        return [
            'message' => 'Validation failed',
            'code' => $exception->getStatusCode(),
            'errors' => $context->getNavigator()->accept($exception->getErrors(), null, $context),
        ];
    }
}
