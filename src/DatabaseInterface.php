<?php

namespace VS\Database;

use VS\Database\Builders\BuilderInterface;
use VS\Database\Builders\Expression;
use VS\Database\Builders\SQL\AbstractBuilder;
use VS\Database\Drivers\DriverInterface;

/**
 * Interface DatabaseInterface
 * @package VS\Framework\Database
 */
interface DatabaseInterface
{
    /**
     * @param string|Expression|AbstractBuilder ...$fields
     * @return mixed
     */
    public function select(...$fields);

    /**
     * @return mixed
     */
    public function beginTransaction();

    /**
     * @return mixed
     */
    public function commitTransaction();

    /**
     * @return mixed
     */
    public function rollbackTransaction();

    /**
     * @param array $columns
     * @param array ...$values
     * @return DatabaseInterface
     */
    public function insert(array $columns, array ...$values);

    /**
     * @param array $data
     * @return DatabaseInterface
     */
    public function update(array $data);

    /**
     * @param string|null $table
     * @return DatabaseInterface
     */
    public function delete(string $table = null);

    /**
     * @param bool $lastOnly
     * @return array
     */
    public static function getQueryLogs(bool $lastOnly = true): array;

    /**
     * @param string|null $collection
     * @return mixed
     */
    public function truncate(string $collection);

    /**
     * @param BuilderInterface|null $builder
     * @return bool|\PDOStatement
     * @throws \Throwable
     */
    public function execute(BuilderInterface $builder = null);

    /**
     * @param string $table
     * @return DatabaseInterface
     */
    public function table(string $table): DatabaseInterface;

    /**
     * @return DriverInterface
     */
    public function getDriver(): DriverInterface;

    /**
     * @param string $sql
     * @param array $bindings
     * @return mixed
     */
    public function query(string $sql, array $bindings = []);
}