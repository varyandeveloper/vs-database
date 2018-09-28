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
    protected const VALUES_ALIAS = '{{values}}';
    protected const DUPLICATE_ALIAS = '{{duplicate}}';

    /**
     * @var array
     */
    protected $onDuplicateData = [];

    /**
     * Insert constructor.
     * @param string|null $table
     * @param string|null $alias
     */
    public function __construct(string $table = null, string $alias = null)
    {
        $this->content = sprintf(
            'INSERT INTO %s (%s) %s %s',
            static::TABLE_ALIAS,
            static::COLUMNS_ALIAS,
            static::VALUES_ALIAS,
            static::DUPLICATE_ALIAS
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
     * @param array $data
     * @return $this
     */
    public function onDuplicateUpdate(array $data)
    {
        $this->onDuplicateData = $data;
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

        $this->content = str_replace(static::VALUES_ALIAS, rtrim($valuesString, ', '), $this->content);
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $onDuplicate = '';

        if (!empty($this->onDuplicateData)) {
            $onDuplicate = 'ON DUPLICATE KEY UPDATE ';
            foreach ($this->onDuplicateData as $field => $value) {

                if (is_numeric($field)) {
                    throw new BuilderException('Invalid numeric field for ON DUPLICATE UPDATE statement');
                }

                $uid = uniqid(':dpu');
                $this->bindings[$uid] = $value;
                $onDuplicate .= sprintf('%s = %s,', Filter::field($field), $uid);
            }
        }

        $this->content = str_replace([
            static::TABLE_ALIAS,
            static::DUPLICATE_ALIAS,
        ], [
            $this->table,
            rtrim($onDuplicate, ',')
        ], $this->content);

        return parent::getContent();
    }
}