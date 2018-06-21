<?php

namespace Vkaf\Bundle\OafBundle\Tests\Validator\Constraints;

use Instasent\SMSCounter\SMSCounter;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Vkaf\Bundle\OafBundle\Validator\Constraints\SmsCount;
use Vkaf\Bundle\OafBundle\Validator\Constraints\SmsCountValidator;

class SmsCountValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new SmsCountValidator(new SMSCounter());
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new SmsCount());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid()
    {
        $this->validator->validate('', new SmsCount());

        $this->assertNoViolation();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testExpectsStringCompatibleType()
    {
        $this->validator->validate(new \stdClass(), new SmsCount());
    }

    public function testValid()
    {
        $value = str_pad('', 306, 'a');
        $constraint = new SmsCount(['max' => 2]);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    public function testInvalid()
    {
        $value = str_pad('', 307, 'a');
        $constraint = new SmsCount(['max' => 2, 'message' => 'msg']);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('msg')->setParameter('{{ limit }}', 2)->assertRaised();
    }
}
