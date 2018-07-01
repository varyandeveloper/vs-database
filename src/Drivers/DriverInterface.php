<?php

namespace VS\Database\Drivers;

/**
 * Interface DriverInterface
 * @package VS\Database\Drivers
 */
interface DriverInterface
{
    /**
     * DriverInterface constructor.
     * @param array $connectionParams
     */
    public function __construct(array $connectionParams);

    /**
     * @return array
     */
    public function getConnectionParams(): array;

    /**
     * @return \PDO|mixed
     */
    public function getAdapter();
}