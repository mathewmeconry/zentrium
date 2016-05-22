<?php

namespace Zentrium\Bundle\CoreBundle\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class JsonDeflateType extends Type
{
    const NAME = 'json_deflate';
    const COMPRESSION_LEVEL = 4;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getBlobTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        return gzdeflate(json_encode($value), self::COMPRESSION_LEVEL);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        if ($value === null || $value === '') {
            return [];
        }

        return json_decode(gzinflate($value), true);
    }

    public function getName()
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
