<?php

namespace VS\Database\Drivers\SQL;

use VS\Database\Drivers\DriverInterface;

/**
 * Class AbstractSQLDriver
 * @package VS\Database\Drivers\SQL
 */
abstract class AbstractSQLDriver implements DriverInterface
{
    /**
     * @var \PDO $PDO
     */
    protected $PDO;

    /**
     * @var array
     */
    protected $connectionParams = [];

    /**
     * MySQLDriver constructor.
     * @param array $connectionParams
     */
    public function __construct(array $connectionParams)
    {
        $this->connectionParams = $connectionParams;
        $this->PDO = new \PDO(...$this->getConnectionParams());
    }

    /**
     * @return \PDO
     */
    public function getAdapter(): \PDO
    {
        return $this->PDO;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    protected function getConnectionParam(string $key)
    {
        return $this->connectionParams[$key] ?? null;
    }

    /**
     * @param string $driverName
     */
    protected function validateDriver(string $driverName)
    {
        if (!in_array($driverName, pdo_drivers())) {
            throw new \RuntimeException('PDO MySQL driver not installed correctly.');
        }
    }
}