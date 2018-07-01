<?php

namespace VS\Database\Builders\SQL;

use VS\Database\Builders\BuilderException;

/**
 * Class Insert
 * @package VS\Framework\Database\Drivers\Decorators\SQL
 */
class Insert extends AbstractBuilder
{
    use TableTrait{
        TableTrait::__construct as private childConstruct;
    }

    protected const COLUMNS_ALIAS = '{{columns}}';

    /**
     * Insert constructor.
     * @param string|null $table
     * @param string|null $alias
     */
    public function __construct(string $table = null, string $alias = null)
    {
        $this->content = sprintf(
            'INSERT INTO %s (%s) ',
            static::TABLE_ALIAS,
            static::COLUMNS_ALIAS
        );
        $this->childConstruct($table, $alias);
    }

    /**
     * @param string ...$fields
     * @return $this
     */
    public function fields(string ...$fields)
    {
        $fieldsString = '';

        foreach ($fields as $field) {
            $fieldsString .= Filter::field($field).',';
        }

        $this->content = str_replace(static::COLUMNS_ALIAS, rtrim($fieldsString, ','), $this->content);
        return $this;
    }

    /**
     * @param Select $select
     * @return $this
     */
    public function select(Select $select)
    {
        $this->content .= $select;
        return $this;
    }

    /**
     * @param array ...$values
     * @return $this
     */
    public function values(array ...$values)
    {
        if (empty($values)) {
            throw new BuilderException('Empty values for insert statement');
        }
        $valuesString = 'VALUES ';

        foreach ($values as $value) {
            $valuesString .= '(';
            foreach ($value as $item) {
                $uid = uniqid(':insert');
                $this->bindings[$uid] = $item;
                $valuesString .= sprintf('%s,', $uid);
            }
            $valuesString = rtrim($valuesString, ',');
            $valuesString .= '), ';
        }

        $this->content .= rtrim($valuesString, ', ');
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->content = str_replace(static::TABLE_ALIAS, $this->table, $this->content);
        return parent::__toString();
    }
}