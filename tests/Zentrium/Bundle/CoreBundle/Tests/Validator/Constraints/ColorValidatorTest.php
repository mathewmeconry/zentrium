<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Validator\Constraints;

use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Zentrium\Bundle\CoreBundle\Validator\Constraints\Color;
use Zentrium\Bundle\CoreBundle\Validator\Constraints\ColorValidator;

class ColorValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new ColorValidator();
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new Color());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid()
    {
        $this->validator->validate('', new Color());

        $this->assertNoViolation();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testExpectsStringCompatibleType()
    {
        $this->validator->validate(new \stdClass(), new Color());
    }

    /**
     * @dataProvider getValidValues
     */
    public function testValidValues($value)
    {
        $constraint = new Color();
        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    public function getValidValues()
    {
        return [
            ['#ffffff'],
            ['#000000'],
            ['#019abf'],
            ['#AABBCC'],
            ['#aaBBcc'],
        ];
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testInvalidValues($value)
    {
        $constraint = new Color([
            'message' => 'myMessage',
        ]);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', '"'.$value.'"')
            ->setCode(Color::INVALID_COLOR_ERROR)
            ->assertRaised();
    }

    public function getInvalidValues()
    {
        return [
            ['#'],
            ['#000'],
            ['#00gg00'],
            ['#00GG00'],
            ['a00000'],
        ];
    }
}
