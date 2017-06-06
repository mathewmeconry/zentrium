<?php

namespace Zentrium\Bundle\CoreBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\JsonSerializationVisitor;

class ValidationExceptionHandler
{
    public function serialize(JsonSerializationVisitor $visitor, $exception, array $type, Context $context)
    {
        $shouldSetRoot = (null === $visitor->getRoot());

        $data = [
            'message' => 'Validation failed',
            'code' => $exception->getStatusCode(),
            'errors' => $visitor->getNavigator()->accept($exception->getErrors(), null, $context),
        ];

        if ($shouldSetRoot) {
            $visitor->setRoot($data);
        }

        return $data;
    }
}
