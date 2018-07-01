<?php

namespace VS\Database;

use VS\Database\Drivers\DriverInterface;

/**
 * Class AbstractDatabase
 * @package VS\Database
 */
abstract class AbstractDatabase
{
    /**
     * @var string $table
     */
    protected $table;
    /**
     * @var
     */
    protected $driver;
    /**
     * @var array $logs
     */
    protected static $logs = [];

    /**
     * Database constructor.
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return DriverInterface
     */
    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    /**
     * @param string $table
     * @return DatabaseInterface
     */
    public function table(string $table): DatabaseInterface
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return bool|mixed|\PDOStatement
     * @throws \Throwable
     */
    public function query(string $sql, array $bindings = [])
    {
        try {
            $statement = $this->driver->getAdapter()->prepare($sql);
            $startTime = microtime(true);
            $row = [
                'query' => $statement->queryString,
                'bindings' => $bindings
            ];
            $statement->execute($bindings);
            $row['time'] = number_format(microtime(true) - $startTime, 4);
            array_unshift(static::$logs, $row);
        } catch (\Throwable $exception) {
            throw $exception;
        }

        return $statement;
    }

    /**
     * @param bool $lastOnly
     * @return array
     */
    public static function getQueryLogs(bool $lastOnly = true): array
    {
        if ($lastOnly) {
            return end(static::$logs);
        }

        return static::$logs;
    }
}