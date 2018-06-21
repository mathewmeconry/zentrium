<?php

namespace Vkaf\Bundle\OafBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SmsCount extends Constraint
{
    public $message = 'This value should fit in {{ limit }} SMS.';
    public $max;
}
