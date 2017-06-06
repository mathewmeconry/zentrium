<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Serializer;

use JMS\Serializer\Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use ReflectionClass;

class StubDriver implements DriverInterface
{
    public function loadMetadataForClass(ReflectionClass $class)
    {
        $metadata = new ClassMetadata($class->name);
        $metadata->fileResources[] = $class->getFileName();

        return $metadata;
    }
}
