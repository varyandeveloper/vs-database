<?php

namespace VS\Database\Drivers;

use VS\Database\Builders\Expression;

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
     * @param string|Expression $field
     * @return string
     */
    public static function field($field): string;

    /**
     * @param string|Expression $alis
     * @return string
     */
    public static function alias($alis): string;

    /**
     * @return array
     */
    public function getConnectionParams(): array;

    /**
     * @return \PDO|mixed
     */
    public function getAdapter();
}