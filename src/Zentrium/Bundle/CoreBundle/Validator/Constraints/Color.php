<?php

namespace Zentrium\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Color extends Constraint
{
    const INVALID_COLOR_ERROR = 'c62a9b5b-45e3-4a82-a5c0-6402744fa25b';

    protected static $errorNames = [
        self::INVALID_COLOR_ERROR => 'INVALID_COLOR_ERROR',
    ];

    public $message = 'This is not a valid hex color code.';
}
