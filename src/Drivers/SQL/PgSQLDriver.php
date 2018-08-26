<?php

namespace VS\Database\Drivers\SQL;

use VS\Database\Builders\Expression;

/**
 * Class PgSQLDriver
 * @package VS\Database\Drivers\SQL
 */
class PgSQLDriver extends AbstractSQLDriver
{
    /**
     * @return array
     */
    public function getConnectionParams(): array
    {
        return [
            sprintf(
                'pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s',
                $this->getConnectionParam('host'),
                $this->getConnectionParam('port'),
                $this->getConnectionParam('database'),
                $this->getConnectionParam('user'),
                $this->getConnectionParam('pass')
            ),
            null,
            null,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL
            ]
        ];
    }

    /**
     * @param string|Expression $field
     * @return string
     */
    public static function field($field): string
    {
        return $field;
    }

    /**
     * @param string|Expression $alis
     * @return string
     */
    public static function alias($alis): string
    {
        return $alis;
    }
}