<?php

/**
 * Class Builder
 */
class Builder extends \PHPUnit\Framework\TestCase
{
    public function testSimpleSelect()
    {
        $select = new \VS\Database\Builders\SQL\Select('users', 'u');
        $expected = 'SELECT * FROM `users` as `u`';
        $this->assertEquals($expected, (string)$select);
    }

    public function testSelectByFields()
    {
        $select = new \VS\Database\Builders\SQL\Select();
        $select->fields('tbl_u.id', 'tbl_u.username')->table('users', 'tbl_u');
        $expected = 'SELECT `tbl_u`.`id`, `tbl_u`.`username` FROM `users` as `tbl_u`';
        $this->assertEquals($expected, (string)$select);
    }

    public function testSelectWhere()
    {
        $select = new \VS\Database\Builders\SQL\Select('users', 'u');
        $select->fields('username')->where->equalTo('id', 1);
        $actual = (string)$select;

        $this->assertCount(1, $select->getBindings());
        $keys = array_keys($select->getBindings());
        $this->assertEquals(1, array_values($select->getBindings())[0]);

        $expected = 'SELECT `username` FROM `users` as `u` WHERE `id` = ' . $keys[0];
        $this->assertEquals($expected, $actual);
    }

    public function testSelectFieldExpression()
    {
        $select = new \VS\Database\Builders\SQL\Select('users', 'u');
        $select->fields(
            new \VS\Database\Builders\Expression('(SELECT `id` FROM `posts` LIMIT 1) as `t`'),
            new \VS\Database\Builders\Expression('CONCAT(`first_name`, " ", `last_name`) as `full_name`')
        );

        $expected = 'SELECT (SELECT `id` FROM `posts` LIMIT 1) as `t`, CONCAT(`first_name`, " ", `last_name`) as `full_name` FROM `users` as `u`';
        $this->assertEquals($expected, ($select));
    }

    public function testSelectFieldSelect()
    {
        $select = new \VS\Database\Builders\SQL\Select('users', 'u');
        $cityNameSelect = new \VS\Database\Builders\SQL\Select('cities');
        $cityNameSelect->fields('name')->as('cityName');
        $select->fields('id', 'username', $cityNameSelect);
        $expected = 'SELECT `id`, `username`, (SELECT `name` FROM `cities`) as `cityName` FROM `users` as `u`';
        $this->assertEquals($expected, (string)$select);
    }

    public function testSelectMultipleConditions()
    {
        $select = new \VS\Database\Builders\SQL\Select('users', 'u');
        $select->where
            ->greaterOrEqualTo('id', 1)
            ->and()
            ->lessOrEqualTo('age', 65)
            ->and();

        $where = new \VS\Database\Builders\SQL\Where();
        $select->where($where->function('length', 'username', 8, \VS\Database\Builders\SQL\AbstractBuilder::GREATER_OPERATOR));

        $actual = (string)$select;
        $this->assertCount(3, $select->getBindings());
        $keys = array_keys($select->getBindings());
        $expected = 'SELECT * FROM `users` as `u` WHERE `id` >= '.$keys[0].' AND `age` <= '.$keys[1].' AND LENGTH(`username`) > ' . $keys[2];
        $this->assertEquals($expected, $actual);
    }
}