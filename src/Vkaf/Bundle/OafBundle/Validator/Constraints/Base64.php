<?php

namespace Vkaf\Bundle\OafBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Base64 extends Constraint
{
    public $message = 'This is not a valid base64 string.';
}
