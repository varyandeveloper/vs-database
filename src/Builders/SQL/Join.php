<?php

namespace VS\Database\Builders\SQL;

/**
 * Class Join
 * @package VS\Database\Builders\SQL
 */
class Join extends AbstractBuilder
{
    use TableTrait;

    const LEFT_JOIN = 1;
    const RIGHT_JOIN = 2;
    const INNER_JOIN = 3;
    const CROSS_JOIN = 4;
    const NATURAL_JOIN = 4;

    const JOINS = [
        self::LEFT_JOIN => 'LEFT OUTER JOIN',
        self::RIGHT_JOIN => 'RIGHT OUTER JOIN',
        self::INNER_JOIN => 'INNER JOIN',
        self::CROSS_JOIN => 'CROSS JOIN',
        self::NATURAL_JOIN => 'NATURAL JOIN',
    ];

    /**
     * @param string $firstField
     * @param string $secondField
     * @param int $joinType
     * @param string $operator
     * @return Join
     */
    public function on(string $firstField, string $secondField = null, int $joinType = self::INNER_JOIN, string $operator = self::EQUAL_OPERATOR): Join
    {
        $onOrWhere = $joinType === static::NATURAL_JOIN ? 'WHERE' : 'ON';

        if (null === $secondField) {
            $this->content .= sprintf(
                '%s %s %s (%s)',
                self::JOINS[$joinType],
                static::TABLE_ALIAS,
                $onOrWhere,
                $firstField
            );
        } else {
            $this->content .= sprintf(
                '%s %s %s %s %s %s ',
                self::JOINS[$joinType],
                static::TABLE_ALIAS,
                $onOrWhere,
                Filter::field($firstField),
                $operator,
                Filter::field($secondField)
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $this->content = str_replace(static::TABLE_ALIAS, $this->table, $this->content);
        return parent::getContent();
    }
}