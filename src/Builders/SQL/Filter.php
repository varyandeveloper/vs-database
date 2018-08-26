<?php

namespace VS\Database\Builders\SQL;

use VS\Database\Builders\Expression;
use VS\Database\Drivers\DriverInterface;

/**
 * Class Filter
 * @package VS\Database\Builders\SQL
 */
class Filter
{
    /**
     * @var DriverInterface
     */
    protected static $driver;

    /**
     * @param DriverInterface $driver
     */
    public static function setDriver(DriverInterface $driver)
    {
        self::$driver = $driver;
    }

    /**
     * @param string|Expression $field
     * @return string
     */
    public static function field($field): string
    {
        return self::$driver::field($field);
    }

    /**
     * @param string|Expression $alias
     * @return string
     */
    public static function alias($alias): string
    {
        return self::$driver::alias($alias);
    }
}