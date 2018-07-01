<?php

namespace VS\Database\Builders\SQL;

use VS\Database\Builders\Expression;

/**
 * Class Filter
 * @package VS\Database\Builders\SQL
 */
class Filter
{
    /**
     * @param string|Expression $field
     * @return string
     */
    public static function field($field)
    {
        if ($field instanceof Expression) {
            return "'$field'";
        }

        if ($field === '*') {
            return $field;
        }

        if (strpos($field, '.') !== FALSE) {
            [$prefix, $suffix] = explode('.', $field);
            if ($suffix !== '*') {
                $suffix = self::alias($suffix);
            }
            $field = "`$prefix`.$suffix";
        } else {
            $field = self::alias($field);
        }

        return $field;
    }

    /**
     * @param string|Expression $alias
     * @return string
     */
    public static function alias($alias): string
    {
        if ($alias instanceof Expression) {
            return "'$alias'";
        }

        if (strpos($alias, 'as ') !== FALSE) {
            [$prefix, $alias] = explode(' ', str_replace('as ', '', $alias));
            $alias = "`$prefix` as `$alias`";
        } else {
            $alias = "`$alias`";
        }

        return $alias;
    }
}