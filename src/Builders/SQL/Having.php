<?php

namespace VS\Database\Builders\SQL;

/**
 * Class Having
 * @package VS\Database\Builders\SQL
 */
class Having extends AbstractBuilder
{
    use ConditionalTrait;

    /**
     * Having constructor.
     */
    public function __construct()
    {
        $this->content = 'HAVING ';
    }
}