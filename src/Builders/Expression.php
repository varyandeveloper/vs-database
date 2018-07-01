<?php

namespace VS\Database\Builders;

/**
 * Class Expression
 * @package VS\Database\Builders
 */
class Expression
{
    /**
     * @var string $content
     */
    protected $content = '';

    /**
     * Expression constructor.
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->content;
    }
}