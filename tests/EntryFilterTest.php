<?php

namespace Smartisan\Settings\Tests;

use Smartisan\Settings\EntryFilter;
use Smartisan\Settings\Exceptions\ModelTypeException;
use Smartisan\Settings\Tests\Models\User;

class EntryFilterTest extends TestCase
{
    public function test_it_can_set_model_filter()
    {
        $filter = new EntryFilter();
        $user = User::create(['name' => 'Mohammed']);

        $this->assertNull($filter->getModel());
        $filter->setModel($user);
        $this->assertInstanceOf(User::class, $filter->getModel());
    }

    public function test_it_can_set_group_filter()
    {
        $filter = new EntryFilter();

        $this->assertNull($filter->getGroup());
        $filter->setGroup('g1');
        $this->assertSame('g1', $filter->getGroup());
    }

    public function test_it_can_set_excepts()
    {
        $filter = new EntryFilter();

        $this->assertNull($filter->getGroup());
        $filter->setExcepts('k1');
        $this->assertSame(['k1'], $filter->getExcepts());

        $filter->setExcepts('k2', 'k3', 'k4');
        $this->assertSame(['k2', 'k3', 'k4'], $filter->getExcepts());

        $filter->setExcepts(['ka', 'kb', 'kc']);
        $this->assertSame(['ka', 'kb', 'kc'], $filter->getExcepts());
    }

    public function test_it_throws_invalid_model_type_exception()
    {
        $filter = new EntryFilter();

        $this->expectException(ModelTypeException::class);
        $filter->setModel('invalid param');
    }

    public function test_it_can_clear_filters()
    {
        $filter = new EntryFilter();

        $user = User::create(['name' => 'Mohammed']);

        $filter->setModel($user)->setGroup('g1')->setExcepts('k1');

        $this->assertInstanceOf(User::class, $filter->getModel());
        $this->assertSame('g1', $filter->getGroup());
        $this->assertSame(['k1'], $filter->getExcepts());

        $filter->clear();
        $this->assertNull($filter->getModel());
        $this->assertNull($filter->getGroup());
        $this->assertSame([], $filter->getExcepts());
    }
}
