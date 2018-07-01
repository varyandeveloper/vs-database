<?php

namespace VS\Database\Builders\SQL;

use VS\Database\Builders\Expression;

/**
 * Trait ConditionalTrait
 * @package VS\Database\Builders\SQL
 */
trait ConditionalTrait
{
    /**
     * @return $this
     */
    public function or()
    {
        $this->content .= ' OR ';
        return $this;
    }

    /**
     * @return $this
     */
    public function and()
    {
        $this->content .= ' AND ';
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @param string $operator
     * @return $this
     */
    public function condition($field, $value, string $operator = self::EQUAL_OPERATOR)
    {
        if (!$field instanceof Expression) {
            $field = Filter::field($field);
        }

        $uid = uniqid(':condition');

        $this->content .= sprintf('%s %s %s', $field, $operator, $uid);
        $this->bindings[$uid] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param string|Expression $field
     * @param mixed $value
     * @param string $operator
     * @return Having|Where
     */
    public function function(string $name, $field, $value = null, string $operator = self::EQUAL_OPERATOR)
    {
        if (!$field instanceof Expression) {
            $field = Filter::field($field);
        }

        if (null !== $value) {
            $uid = uniqid(':condition');
            $this->bindings[$uid] = $value;
            $this->content .= sprintf('%s(%s) %s %s', strtoupper($name), $field, $operator, $uid);
        } else {
            $this->content .= sprintf('%s(%s)', strtoupper($name), $field);
        }

        return $this;
    }
}