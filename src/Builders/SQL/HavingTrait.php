<?php

namespace VS\Database\Builders\SQL;

/**
 * Trait HavingTrait
 * @package VS\Database\Builders\SQL
 */
trait HavingTrait
{
    /**
     * @var Having $having
     */
    public $having;

    /**
     * @param Having $having
     * @return AbstractBuilder
     */
    public function having(Having $having): AbstractBuilder
    {
        $this->bindings += $this->having->getBindings() + $having->getBindings();
        $this->having .= str_replace('HAVING', '', $having);
        return $this;
    }

    /**
     * @return void
     */
    protected function bindHaving()
    {
        if (trim($this->having) === 'HAVING') {
            $this->having = '';
        }

        if ($this->having instanceof Having) {
            $this->bindings += $this->having->getBindings();
        }

        $this->content = str_replace(self::HAVING_ALIAS, $this->having, $this->content);
    }
}