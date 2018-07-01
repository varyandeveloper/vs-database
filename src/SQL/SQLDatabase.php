<?php

namespace VS\Database\SQL;

use VS\Database\AbstractDatabase;
use VS\Database\Builders\BuilderInterface;
use VS\Database\Builders\SQL\{
    AbstractBuilder, Delete, Insert, Join, Update, Select, Where, Replace
};
use VS\Database\Drivers\SQL\AbstractSQLDriver;
use VS\General\DIFactory;

/**
 * Class SQLDatabase
 * @package VS\Framework\Database
 * @property Where $where
 * @method \PDOStatement query(string $sql, array $bindings = [])
 * @method SQLDatabase where(Where $where)
 */
class SQLDatabase extends AbstractDatabase
{
    /**
     * @var AbstractBuilder|Select|Update|Delete|Insert
     */
    protected $builder;

    /**
     * SQLDatabase constructor.
     * @param AbstractSQLDriver $driver
     */
    public function __construct(AbstractSQLDriver $driver)
    {
        parent::__construct($driver);
    }

    /**
     * @param BuilderInterface $builder
     * @return \PDOStatement
     * @throws \Throwable
     */
    public function execute(BuilderInterface $builder = null): \PDOStatement
    {
        if (null === $builder) {
            $this->builder->table($this->table);
            $builder = $this->builder;
        }

        return $this->query($builder->__toString(), $builder->getBindings());
    }

    /**
     * @param array $map
     * @param array ...$values
     * @return SQLDatabase
     * @throws \ReflectionException
     * @throws \VS\General\Exceptions\ClassNotFoundException
     */
    public function insert(array $map, array ...$values): SQLDatabase
    {
        $this->getBuilder(Insert::class)->fields(...$map)
            ->values(...$values);
        return $this;
    }

    /**
     * @param $joinTable
     * @param null $firstColumn
     * @param null $secondColumn
     * @param int $joinType
     * @param string $operator
     * @return SQLDatabase
     * @throws \ReflectionException
     * @throws \VS\General\Exceptions\ClassNotFoundException
     */
    public function join($joinTable, $firstColumn = null, $secondColumn = null, int $joinType = Join::INNER_JOIN, string $operator = Join::EQUAL_OPERATOR): SQLDatabase
    {
        if ($joinTable instanceof Join) {
            $this->getBuilder()->join($joinTable);
        } else {
            $join = new Join($joinTable);
            $join->on($firstColumn, $secondColumn, $joinType, $operator);
            $this->getBuilder()->join($join);
        }

        return $this;
    }

    /**
     * @param string ...$fields
     * @return SQLDatabase
     * @throws \ReflectionException
     * @throws \VS\General\Exceptions\ClassNotFoundException
     */
    public function select(string ...$fields): SQLDatabase
    {
        $this->getBuilder()->fields(...$fields);
        return $this;
    }

    /**
     * @param array $data
     * @return SQLDatabase
     * @throws \ReflectionException
     * @throws \VS\General\Exceptions\ClassNotFoundException
     */
    public function update(array $data): SQLDatabase
    {
        $this->builder = $this->getBuilder(Update::class);
        foreach ($data as $field => $datum) {
            if ($datum instanceof Select) {
                $this->builder->fieldsBySelect($datum, $field);
                unset($data[$field]);
            }
        }
        $this->builder->fieldToValue($data);
        return $this;
    }

    /**
     * @param array $map
     * @param array ...$values
     * @return $this
     * @throws \ReflectionException
     * @throws \VS\General\Exceptions\ClassNotFoundException
     */
    public function replace(array $map, array ...$values)
    {
        $this->getBuilder(Replace::class)->fields(...$map)
            ->values(...$values);
        return $this;
    }

    /**
     * @param string|null $table
     * @return SQLDatabase
     * @throws \ReflectionException
     * @throws \VS\General\Exceptions\ClassNotFoundException
     */
    public function delete(string $table = null): SQLDatabase
    {
        $this->builder = $this->getBuilder(Delete::class);
        if (null !== $table) {
            $this->table($table);
        }
        return $this;
    }

    /**
     * @param string|null $table
     * @throws \Throwable
     */
    public function truncate(string $table = null)
    {
        if (null !== $table) {
            $this->table = $table;
        }
        $this->query('TRUNCATE TABLE ' . $this->table);
    }

    /**
     * @param $name
     * @return AbstractBuilder
     */
    public function __get($name): AbstractBuilder
    {
        if (property_exists($this->builder, $name)) {
            return $this->builder->{$name};
        }

        throw new \InvalidArgumentException(sprintf(
            'Class %s dose not have %s property',
            get_class(),
            $name
        ));
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return SQLDatabase
     * @throws \ReflectionException
     * @throws \VS\General\Exceptions\ClassNotFoundException
     */
    public function __call(string $method, array $arguments): SQLDatabase
    {
        if (method_exists($this->builder, $method)) {
            $this->getBuilder()->{$method}(...$arguments);
        }

        return $this;
    }

    /**
     * @param string $builderClass
     * @return AbstractBuilder|Select|Update|Insert|Delete|Replace
     * @throws \ReflectionException
     * @throws \VS\General\Exceptions\ClassNotFoundException
     */
    protected function getBuilder(string $builderClass = Select::class): AbstractBuilder
    {
        if (!$this->builder instanceof $builderClass) {
            $this->builder = DIFactory::injectClass($builderClass);
        }
        return $this->builder;
    }
}