<?php

namespace VS\Database\Builders\SQL;

/**
 * Class Delete
 * @package VS\Database\Builders\SQL
 */
class Delete extends AbstractBuilder
{
    use WhereTrait, JoinTrait, TableTrait{
        TableTrait::__construct as private childConstruct;
    }

    /**
     * Delete constructor.
     * @param string|null $table
     */
    public function __construct(string $table = null)
    {
        $this->content = sprintf(
            'DELETE FROM ' . static::TABLE_ALIAS . ' %s',
            static::WHERE_ALIAS
        );
        $this->childConstruct($table);
        $this->initialize();
    }

    /**
     * Delete clone.
     */
    public function __clone()
    {
        $this->initialize();
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $this->bindWhere();
        $this->bindJoin();
        $this->content = str_replace(static::TABLE_ALIAS, $this->table, $this->content);
        return parent::getContent();
    }

    /**
     * @return void
     */
    protected function initialize(): void
    {
        $this->where = new Where();
        $this->join = new Join();
    }
}