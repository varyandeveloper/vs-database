<?php

namespace VS\Database\Builders\SQL;

use VS\Database\Builders\Expression;

/**
 * Class Select
 * @package VS\Framework\Database\Drivers\Decorators\SQL
 * @method Select where(Where $where): AbstractSQLBuilder
 * @method Select join(Join $join): AbstractSQLBuilder
 */
class Select extends AbstractBuilder
{
    use WhereTrait, HavingTrait, JoinTrait, TableTrait{
        TableTrait::__construct as private childConstruct;
    }

    protected const SELECT_FIELDS_ALIAS = '{{fields}}';
    protected const LIMIT_ALIAS = '{{LIMIT}}';

    /**
     * @var array $fields
     */
    protected $fields;
    /**
     * @var string $alias
     */
    protected $as;
    /**
     * @var array $groupBy
     */
    protected $groupBy = [];
    /**
     * @var array $orderBy
     */
    protected $orderBy = [];
    /**
     * @var array $limit
     */
    protected $limit = [];

    /**
     * Select constructor.
     * @param string|null $table
     * @param string|null $alias
     */
    public function __construct(string $table = null, string $alias = null)
    {
        $this->fields = ['*'];
        $this->content = sprintf(
            'SELECT %s FROM %s %s %s %s %s %s %s',
            static::SELECT_FIELDS_ALIAS,
            static::TABLE_ALIAS,
            static::JOIN_ALIAS,
            static::WHERE_ALIAS,
            static::GROUP_BY_ALIAS,
            static::HAVING_ALIAS,
            static::ORDER_BY_ALIAS,
            static::LIMIT_ALIAS
        );
        $this->childConstruct($table, $alias);
        $this->initialize();
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->initialize();
    }

    /**
     * @param string|Expression ...$fields
     * @return Select
     */
    public function fields(...$fields): Select
    {
        if (!empty($fields)) {
            $this->fields = $fields;
        }

        foreach ($this->fields as &$field) {

            if ($field instanceof Select) {
                $this->bindings += $field->getBindings();
                continue;
            }

            if (!$field instanceof Expression) {
                $field = Filter::field($field);
            }
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param string ...$fields
     * @return Select
     */
    public function groupBy(string ...$fields): Select
    {
        $this->groupBy = $fields;
        return $this;
    }

    /**
     * @param array $order
     * @return Select
     */
    public function orderBy(array $order): Select
    {
        $this->orderBy = $order;
        return $this;
    }

    /**
     * @param int $limit
     * @param int|null $offset
     * @return $this
     */
    public function limit(int $limit, ?int $offset = null)
    {
        $this->limit = [
            'limit' => $limit,
            'offset' => (int)$offset
        ];
        return $this;
    }

    /**
     * @param string $alias
     * @return Select
     */
    public function as(string $alias): Select
    {
        $this->as = Filter::alias($alias);
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $this->bindJoin();
        $this->bindWhere();
        $this->bindGroupBy();
        $this->bindHaving();
        $this->bindOrderBy();
        $this->bindLimit();

        $this->content = str_replace([
            self::TABLE_ALIAS,
            self::SELECT_FIELDS_ALIAS,
        ],[
            $this->table,
            implode(', ', $this->fields)
        ], $this->content);

        if (null !== $this->as) {
            $this->content = sprintf('(%s) as %s', $this->content, $this->as);
        }

        return parent::getContent();
    }

    /**
     * @return void
     */
    protected function bindLimit()
    {
        $limitString = '';
        if (!empty($this->limit)) {
            $limitString = 'LIMIT ' . (int)$this->limit['limit'] . ' OFFSET ' . (int)$this->limit['offset'];
        }

        $this->content = str_replace(static::LIMIT_ALIAS, $limitString, $this->content);
    }

    /**
     * @return void
     */
    protected function bindGroupBy(): void
    {
        $groupString = '';
        if (!empty($this->groupBy)) {
            $groupString = sprintf('GROUP BY %s', implode(',', $this->groupBy));
        }
        $this->content = str_replace(static::GROUP_BY_ALIAS, $groupString, $this->content);
    }

    /**
     * @return void
     */
    protected function bindOrderBy(): void
    {
        $orderString = null;
        if (!empty($this->orderBy)) {
            $orderString = 'ORDER BY ';
            foreach ($this->orderBy as $field => $type) {
                if (is_numeric($field)) {
                    $field = $type;
                    $type = "ASC";
                }
                $orderString .= sprintf('%s %s,', Filter::field($field), strtoupper($type));
            }
            $orderString = rtrim($orderString, ',');
        }
        $this->content = str_replace(static::ORDER_BY_ALIAS, $orderString, $this->content);
    }

    /**
     * Create instances of join and where
     * @return void
     */
    protected function initialize(): void
    {
        $this->where = new Where();
        $this->join = new Join();
        $this->having = new Having();
    }
}