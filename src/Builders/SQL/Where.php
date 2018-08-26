<?php

namespace VS\Database\Builders\SQL;

use VS\Database\Builders\Expression;
use VS\DIContainer\Injector\Injector;


/**
 * Class Where
 * @package VS\Database\Builders\SQL
 */
class Where extends AbstractBuilder
{
    use ConditionalTrait;

    /**
     * Where constructor.
     */
    public function __construct()
    {
        $this->content = 'WHERE ';
    }

    /**
     * @param $field
     * @param $values
     * @return Where
     * @throws \VS\DIContainer\Injector\InjectorException
     */
    public function in($field, $values): Where
    {
        $valuesString = $values;

        if (is_callable($values)) {
            $valuesString = trim(Injector::injectFunction($values));
            if (empty($valuesString)) {
                throw new \InvalidArgumentException('Callable should return either SQL or ' . AbstractBuilder::class);
            }
        } elseif (is_array($values)) {
            $valuesString = $this->getArrayValuesString($values);
        }

        if (empty($valuesString)) {
            $valuesString = "''";
        }

        $this->content .= sprintf(' %s IN (%s) ', $this->fieldResolve($field), $valuesString);
        return $this;
    }

    /**
     * @param string $subQuery
     * @return Where
     */
    public function exists(string $subQuery): Where
    {
        $this->content .= sprintf('EXISTS (%s) ', $subQuery);
        return $this;
    }

    /**
     * @param string $subQuery
     * @return Where
     */
    public function notExists(string $subQuery): Where
    {
        $this->content .= sprintf('!EXISTS (%s) ', $subQuery);
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @return Where
     */
    public function equalTo($field, $value): Where
    {
        return $this->condition($field, $value, static::EQUAL_OPERATOR);
    }

    /**
     * @param string $field
     * @param $value
     * @return Where
     */
    public function notEqualTo($field, $value): Where
    {
        return $this->condition($field, $value, static::NOT_EQUAL_OPERATOR);
    }

    /**
     * @param string $field
     * @param $value
     * @return Where
     */
    public function greaterOrEqualTo($field, $value): Where
    {
        return $this->condition($field, $value, static::GREATER_OR_EQUAL_OPERATOR);
    }

    /**
     * @param string $field
     * @param $value
     * @return Where
     */
    public function lessOrEqualTo(string $field, $value): Where
    {
        return $this->condition($field, $value, static::LESS_OR_EQUAL_OPERATOR);
    }

    /**
     * @param string $field
     * @param $value
     * @param int $likeType
     * @return Where
     */
    public function like($field, $value, int $likeType = self::LIKE_TYPE_FULL): Where
    {
        if ($likeType === static::LIKE_TYPE_END) {
            $value = "$value%";
        } elseif ($likeType === static::LIKE_TYPE_BEGINNING) {
            $value = "%$value";
        } else {
            $value = "%$value%";
        }
        return $this->condition($field, $value, static::LIKE_OPERATOR);
    }

    /**
     * @param string $expression
     * @return $this
     */
    public function expression(string $expression)
    {
        $this->content .= $expression . ' ';
        return $this;
    }

    /**
     * @param array $values
     * @return string
     */
    protected function getArrayValuesString(array $values): string
    {
        $valuesString = '';

        foreach ($values as $value) {
            $uid = uniqid(':where');
            $this->bindings[$uid] = $value;
            $valuesString .= sprintf('%s, ', $uid);
        }

        return rtrim($valuesString, ', ');
    }

    /**
     * @param $field
     */
    protected function fieldResolve(&$field)
    {
        if (!$field instanceof Expression) {
            $field = Filter::field($field);
        }
    }
}