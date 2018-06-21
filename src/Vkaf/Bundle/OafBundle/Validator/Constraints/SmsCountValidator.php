<?php

namespace Vkaf\Bundle\OafBundle\Validator\Constraints;

use Instasent\SMSCounter\SMSCounter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SmsCountValidator extends ConstraintValidator
{
    private $counter;

    public function __construct(SMSCounter $counter)
    {
        $this->counter = $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SmsCount) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\SmsCount');
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $count = $this->counter->count($value)->messages;
        if ($count > $constraint->max) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ limit }}', $constraint->max)
                ->addViolation()
            ;
        }
    }
}
