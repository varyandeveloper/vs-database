<?php

namespace VS\Database\Builders\SQL;

/**
 * Trait WhereTrait
 * @package VS\Framework\Database\Drivers\Decorators\SQL
 */
trait WhereTrait
{
    /**
     * @var Where $where
     */
    public $where;

    /**
     * @param Where $where
     * @return Delete
     */
    public function where(Where $where): AbstractBuilder
    {
        $this->bindings += $this->where->getBindings() + $where->getBindings();
        $this->where .= str_replace('WHERE', '', $where);
        return $this;
    }

    /**
     * @return void
     */
    protected function bindWhere()
    {
        if (trim($this->where) === 'WHERE') {
            $this->where = '';
        }

        if ($this->where instanceof Where) {
            $this->bindings += $this->where->getBindings();
        }

        $this->content = str_replace(self::WHERE_ALIAS, $this->where, $this->content);
    }
}