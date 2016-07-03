<?php

namespace Zentrium\Bundle\CoreBundle\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\JsonSerializationVisitor;

class RelationHandler
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function serializeRelationCollection(JsonSerializationVisitor $visitor, $collection, array $type, Context $context)
    {
        $ids = [];
        foreach ($collection as $object) {
            $ids[] = $this->serializeRelation($visitor, $object, $type, $context);
        }

        return $ids;
    }

    public function serializeRelation(JsonSerializationVisitor $visitor, $object, array $type, Context $context)
    {
        $metadata = $this->manager->getClassMetadata(get_class($object));

        $id = $metadata->getIdentifierValues($object);
        if (!$metadata->isIdentifierComposite) {
            $id = array_shift($id);
        }

        return $id;
    }
}
