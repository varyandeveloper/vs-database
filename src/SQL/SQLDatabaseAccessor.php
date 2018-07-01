<?php

namespace VS\Database\SQL;

use VS\Container\ClassAccessor\AbstractClassAccessor;

/**
 * Class SQLDatabaseAccessor
 * @package VS\Framework\Database\Drivers
 */
class SQLDatabaseAccessor extends AbstractClassAccessor
{
    /**
     * @return string
     */
    public static function getClass(): string
    {
        return SQLDatabase::class;
    }
}