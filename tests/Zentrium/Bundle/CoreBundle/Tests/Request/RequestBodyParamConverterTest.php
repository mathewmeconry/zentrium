<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Zentrium\Bundle\CoreBundle\Request\RequestBodyParamConverter;

class RequestBodyParamConverterTest extends \PHPUnit_Framework_TestCase
{
    private $converter;

    public function setUp()
    {
        $serializer = $this->getMockBuilder('FOS\RestBundle\Serializer\Serializer')->getMock();

        $error = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolationInterface')->getMock();

        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')->getMock();
        $validator
            ->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList([$error])))
        ;

        $this->converter = new RequestBodyParamConverter($serializer, null, null, $validator, 'validationErrors');
    }

    /**
     * @expectedException \Zentrium\Bundle\CoreBundle\Validator\ValidationException
     */
    public function testInvalidBody()
    {
        $request = new Request();
        $configuration = new ParamConverter([]);

        $this->converter->apply($request, $configuration);
    }

    public function testInvalidBodyWithoutRejection()
    {
        $request = new Request();
        $configuration = new ParamConverter([
            'options' => [
                'rejectInvalid' => false,
            ],
        ]);

        $this->assertTrue($this->converter->apply($request, $configuration));
    }
}
