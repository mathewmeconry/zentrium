<?php

namespace Zentrium\Bundle\TimesheetBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;
use Zentrium\Bundle\TimesheetBundle\Entity\EntryManager;

class NotOverlappingValidator extends ConstraintValidator
{
    private $manager;

    public function __construct(EntryManager $manager)
    {
        $this->manager = $manager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof Entry) {
            throw new UnexpectedTypeException($value, 'Zentrium\Bundle\TimesheetBundle\Entity\Entry');
        }

        if ($value->getUser() === null || $value->getStart() === null || $value->getEnd() === null) {
            return;
        }

        if ($this->manager->isOverlapping($value)) {
            $this->context->buildViolation($constraint->message)
                ->setCode(NotOverlapping::NOT_OVERLAPPING_ERROR)
                ->addViolation();
        }
    }
}
