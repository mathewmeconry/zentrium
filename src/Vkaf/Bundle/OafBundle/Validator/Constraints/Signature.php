<?php

namespace Vkaf\Bundle\OafBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Signature extends Constraint
{
    public $message = 'This value is not a valid signature.';
    public $minStrokes = 1;
}
