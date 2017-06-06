<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Serializer;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Builder\CallbackDriverFactory;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\ConstraintViolationHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Zentrium\Bundle\CoreBundle\Serializer\ValidationExceptionHandler;
use Zentrium\Bundle\CoreBundle\Validator\ValidationException;

class ValidationExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $serializer;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()
            ->setMetadataDriverFactory(new CallbackDriverFactory(function (array $metadataDirectories, Reader $reader) {
                return new StubDriver();
            }))
            ->addDefaultHandlers()
            ->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new ConstraintViolationHandler());
                $registry->registerHandler(GraphNavigator::DIRECTION_SERIALIZATION, ValidationException::class, 'json', [new ValidationExceptionHandler(), 'serialize']);
            })
            ->build()
        ;
    }

    public function testSerialize()
    {
        $errors = new ConstraintViolationList([
            new ConstraintViolation('violation', 'template', [], null, 'path.to.property', null),
        ]);
        $exception = new ValidationException($errors);

        return $this->assertJsonStringEqualsJsonString(
            json_encode([
                'message' => 'Validation failed',
                'code' => 400,
                'errors' => [
                    [
                        'message' => 'violation',
                        'property_path' => 'path.to.property',
                    ],
                ],
            ]),
            $this->serializer->serialize($exception, 'json')
        );
    }
}
