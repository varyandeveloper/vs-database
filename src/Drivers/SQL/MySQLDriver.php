<?php

namespace VS\Database\Drivers\SQL;

use VS\Database\Builders\Expression;

/**
 * Class MySQLDriver
 * @package VS\Database\Drivers\SQL
 */
class MySQLDriver extends AbstractSQLDriver
{
    /**
     * @return array
     */
    public function getConnectionParams(): array
    {
        $this->validateDriver('mysql');

        return [
            sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $this->getConnectionParam('host'),
                $this->getConnectionParam('database'),
                $this->getConnectionParam('charset')
            ),
            $this->getConnectionParam('user'),
            $this->getConnectionParam('pass'),
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_EMPTY_STRING,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
            ]
        ];
    }

    /**
     * @param string|Expression $field
     * @return string
     */
    public static function field($field): string
    {
        if ($field instanceof Expression) {
            return "'$field'";
        }

        if ($field === '*') {
            return $field;
        }

        if (strpos($field, '.') !== FALSE) {
            [$prefix, $suffix] = explode('.', $field);
            if ($suffix !== '*') {
                $suffix = self::alias($suffix);
            }
            $field = "`$prefix`.$suffix";
        } else {
            $field = self::alias($field);
        }

        return $field;
    }

    /**
     * @param string|Expression $alias
     * @return string
     */
    public static function alias($alias): string
    {
        if ($alias instanceof Expression) {
            return "'$alias'";
        }

        if (strpos($alias, 'as ') !== FALSE) {
            [$prefix, $alias] = explode(' ', str_replace('as ', '', $alias));
            $alias = "`$prefix` as `$alias`";
        } else {
            $alias = "`$alias`";
        }

        return $alias;
    }
}