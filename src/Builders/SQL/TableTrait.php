<?php

namespace VS\Database\Builders\SQL;

/**
 * Trait TableTrait
 * @package VS\Database\Builders\SQL
 */
trait TableTrait
{
    /**
     * @var string $table
     */
    protected $table;
    /**
     * @var string $alias
     */
    protected $alias;

    /**
     * CommonMethodsTrait constructor.
     * @param null|string $table
     * @param null|string $alias
     */
    public function __construct(?string $table = null, ?string $alias = null)
    {
        if (null !== $table) {
            $params = [$table];
            if (null !== $alias) {
                $params[] = $alias;
            }
            $this->table(...$params);
        }
    }

    /**
     * @param string $tableName
     * @param string|null $as
     * @return AbstractBuilder|$this
     */
    public function table(string $tableName, string $as = null)
    {
        if (null !== $as) {
            $tableName .= ' as ' . $as;
        }
        $this->table = Filter::alias($tableName);
        $this->alias = $as;
        return $this;
    }

    /**
     * @param string $alias
     * @return AbstractBuilder|$this
     */
    public function setTableAlias(string $alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableAliasAlias(): string
    {
        return $this->alias;
    }

}