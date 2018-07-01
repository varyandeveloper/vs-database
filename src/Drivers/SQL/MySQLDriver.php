<?php

namespace VS\Database\Drivers\SQL;

use PDO;

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
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
            ]
        ];
    }
}