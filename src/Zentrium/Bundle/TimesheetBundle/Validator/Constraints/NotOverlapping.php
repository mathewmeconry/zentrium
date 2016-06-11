<?php

namespace Zentrium\Bundle\TimesheetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotOverlapping extends Constraint
{
    const NOT_OVERLAPPING_ERROR = 'c51e734b-0542-4efa-a0e3-395207ac7d9b';

    public $message = 'This entry overlaps with another entry.';

    protected static $errorNames = [
        self::NOT_OVERLAPPING_ERROR => 'NOT_OVERLAPPING_ERROR',
    ];

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
