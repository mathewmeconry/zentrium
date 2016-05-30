<?php

namespace Zentrium\Bundle\CoreBundle\Doctrine;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UtcDateTimeType extends DateTimeType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof DateTimeImmutable) {
            $value = $value->setTimezone(self::getUtcZone());
        } elseif ($value instanceof DateTime) {
            $value = clone $value;
            $value->setTimezone(self::getUtcZone());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $value;
        }

        $converted = DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::getUtcZone());

        if (!$converted) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        $converted->setTimezone(self::getDefaultZone());

        return $converted;
    }

    private static function getUtcZone()
    {
        static $utc;
        if (!$utc) {
            $utc = new DateTimeZone('UTC');
        }

        return $utc;
    }

    private static function getDefaultZone()
    {
        static $default;
        if (!$default) {
            $default = new DateTimeZone(date_default_timezone_get());
        }

        return $default;
    }
}
