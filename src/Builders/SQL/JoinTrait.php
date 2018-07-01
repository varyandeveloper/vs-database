<?php

namespace VS\Database\Builders\SQL;

/**
 * Trait JoinTrait
 * @package VS\Database\Builders\SQL
 */
trait JoinTrait
{
    /**
     * @var Join $join
     */
    protected $join;

    /**
     * @param Join $join
     * @return AbstractBuilder
     */
    public function join(Join $join): AbstractBuilder
    {
        $this->join .= $join;
        return $this;
    }

    /**
     * @return void
     */
    protected function bindJoin()
    {
        if (empty($this->join)) {
            $this->join = '';
        }

        if ($this->join instanceof Join) {
            $this->bindings += $this->join->getBindings();
        }

        $this->content = str_replace(static::JOIN_ALIAS, $this->join, $this->content);
    }
}