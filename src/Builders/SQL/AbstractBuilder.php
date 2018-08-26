<?php

namespace VS\Database\Builders\SQL;

use VS\Database\Builders\BuilderInterface;

/**
 * Class AbstractBuilder
 * @package VS\Database\Builders\SQL
 */
abstract class AbstractBuilder implements  BuilderInterface
{
    const TABLE_ALIAS = '{{table}}';
    const NOT_EQUAL_OPERATOR = '<>';
    const EQUAL_OPERATOR = '=';
    const GREATER_OR_EQUAL_OPERATOR = '>=';
    const LESS_OR_EQUAL_OPERATOR = '<=';
    const LESS_OPERATOR = '<';
    const GREATER_OPERATOR = '>';
    const LIKE_OPERATOR = 'LIKE';
    const LIKE_TYPE_FULL = 1;
    const LIKE_TYPE_BEGINNING = 2;
    const LIKE_TYPE_END = 3;
    protected const WHERE_ALIAS = '{{where}}';
    protected const HAVING_ALIAS = '{{having}}';
    protected const GROUP_BY_ALIAS = '{{group_by}}';
    protected const ORDER_BY_ALIAS = '{{order_by}}';
    protected const JOIN_ALIAS = '{{join}}';

    /**
     * @var array $bindings
     */
    protected $bindings = [];

    /**
     * @var string $content
     */
    protected $content = '';

    /**
     * @return $this
     */
    public function open()
    {
        $this->content .= '( ';
        return $this;
    }

    /**
     * @return $this
     */
    public function close()
    {
        $this->content .= ') ';
        return $this;
    }

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * @param array $bindings
     */
    public function setBindings(array $bindings)
    {
        $this->bindings = $bindings;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return str_replace(['  ', ' ,', ' )', '( '], [' ', ',', ')', '('], trim($this->content));
    }
}