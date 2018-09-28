<?php

namespace VS\Database\Builders\SQL;
use VS\Database\Builders\Expression;

/**
 * Class Update
 * @package VS\Database\Builders\SQL
 */
class Update extends AbstractBuilder
{
    use WhereTrait, JoinTrait, TableTrait{
        TableTrait::__construct as private childConstruct;
    }

    protected const SELECT_ALIAS = '{{select}}';
    protected const JOIN_ALIAS = '{{join}}';
    protected const SET_ALIAS = '{{set}}';
    protected const SELECT_SET_ALIAS = '{{select_set}}';
    protected const WHERE_ALIAS = '{{where}}';

    private static $aliasCounter = 0;

    /**
     * @var array $fieldToValue
     */
    protected $fieldToValue = [];
    /**
     * @var string $selectString
     */
    private $selectString;
    /**
     * @var string $selectSetString
     */
    private $selectSetString;

    /**
     * Update constructor.
     * @param null|string $table
     * @param null|string $alias
     */
    public function __construct(string $table = null, string $alias = null)
    {
        $this->content = sprintf(
            'UPDATE %s %s %s SET %s %s %s',
            static::TABLE_ALIAS,
            static::JOIN_ALIAS,
            static::SELECT_ALIAS,
            static::SET_ALIAS,
            static::SELECT_SET_ALIAS,
            static::WHERE_ALIAS
        );
        $this->childConstruct($table, $alias);
        $this->initialize();
    }

    public function __clone()
    {
        $this->initialize();
    }

    /**
     * @param array $fieldToValue
     * @return Update
     */
    public function fieldToValue(array $fieldToValue): Update
    {
        foreach ($fieldToValue as $field => $value) {

            if ($value instanceof Expression) {
                $this->fieldToValue[$field] = $value;
            } else {
                $uid = uniqid(':update');
                $this->fieldToValue[$field] = $uid;
                $this->bindings[$uid] = $value;
            }

        }
        return $this;
    }

    /**
     * @param Select $select
     * @param string ...$fields
     * @return Update
     */
    public function fieldsBySelect(Select $select, string ...$fields): Update
    {
        $selectFields = $select->getFields();
        $this->selectString .= sprintf(', (%s) as `update_alias_%d`', $select, ++self::$aliasCounter);

        foreach ($fields as $i => $field) {
            $this->selectSetString .= sprintf(
                '%s %s `update_alias_%d`.%s, ',
                Filter::field($field),
                self::EQUAL_OPERATOR,
                self::$aliasCounter,
                $selectFields[$i]
            );
            $this->bindings += $select->getBindings();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $this->bindWhere();
        $this->bindJoin();

        $setString = $this->getSetString();
        $this->content = rtrim(str_replace(
            [static::TABLE_ALIAS, static::SELECT_ALIAS, static::SELECT_SET_ALIAS, static::SET_ALIAS],
            [$this->table, $this->selectString, rtrim($this->selectSetString, ', '), rtrim($setString, ', ')],
            $this->content
        ), ', ');

        return parent::getContent();
    }

    /**
     * @return string
     */
    protected function getSetString(): string
    {
        $setString = '';
        foreach ($this->fieldToValue as $field => $value) {
            $setString .= sprintf(' %s = %s, ', Filter::field($field), $value);
        }

        if (empty($this->selectString)) {
            $this->selectSetString = '';
        } else {
            $this->selectSetString .= $setString;
            $setString = '';
        }

        return $setString;
    }

    /**
     * @return void
     */
    protected function initialize(): void
    {
        $this->where = new Where();
    }
}