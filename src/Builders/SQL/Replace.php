<?php

namespace VS\Database\Builders\SQL;

/**
 * Class Replace
 * @package VS\Database\Builders\SQL
 */
class Replace extends Insert
{
    use WhereTrait, JoinTrait, TableTrait{
        TableTrait::__construct as private childConstruct;
    }

    /**
     * Replace constructor.
     * @param null|string $table
     * @param null|string $alias
     */
    public function __construct(?string $table = null, ?string $alias = null)
    {
        $this->childConstruct($table, $alias);
        parent::__construct($table, $alias);
        $this->content = sprintf(
            'REPLACE INTO %s (%s) ',
            static::TABLE_ALIAS,
            static::COLUMNS_ALIAS
        );
    }
}