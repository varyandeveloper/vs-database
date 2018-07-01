<?php

namespace VS\Database\Builders;

/**
 * Interface BuilderInterface
 */
interface BuilderInterface
{
    /**
     * @return array
     */
    public function getBindings(): array;

    /**
     * @return string
     */
    public function __toString(): string;
}