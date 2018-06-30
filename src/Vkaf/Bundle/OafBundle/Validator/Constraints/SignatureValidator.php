<?php

namespace Vkaf\Bundle\OafBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SignatureValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Signature) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Signature');
        }

        if (null === $value) {
            return;
        }

        if (!is_array($value) || count($value) < $constraint->minStrokes) {
            $this->context->buildViolation($constraint->message)->addViolation();

            return;
        }

        foreach ($value as $stroke) {
            if (!$this->validateStroke($stroke)) {
                $this->context->buildViolation($constraint->message)->addViolation();

                return;
            }
        }
    }

    private function validateStroke($stroke)
    {
        if (!is_array($stroke) || count($stroke) < 1) {
            return false;
        }

        $lastTime = null;
        foreach ($stroke as $point) {
            if (!is_array($point) || count($point) !== 3) {
                return false;
            }
            list($time, $x, $y) = $point;
            if (!$this->isNumber($time) || !$this->isNumber($x) || !$this->isNumber($y)) {
                return false;
            }
            if ($lastTime === null ? $time !== 0 : $time < $lastTime) {
                return false;
            }
            $lastTime = $time;
        }

        return true;
    }

    private function isNumber($number)
    {
        return is_int($number) || is_float($number);
    }
}
