<?php

namespace Smartisan\Settings\Tests;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Settings;

class SettingsCastsTest extends TestCase
{
    public function test_carbon_cast()
    {
        $date = Carbon::create(2021, 5, 1);

        $this->assertCount(0, Settings::all());

        Settings::set('k', $date);

        $this->assertCount(1, Settings::all());
        $this->assertInstanceOf(Carbon::class, Settings::get('k'));
        $this->assertSame($date->format('Y-m-d'), Settings::get('k')->format('Y-m-d'));
    }

    public function test_carbon_period_cast()
    {
        $start = Carbon::create(2021, 5, 1);
        $end = Carbon::create(2021, 5, 15);

        $period = CarbonPeriod::create($start, $end);

        $this->assertCount(0, Settings::all());

        Settings::set('k', $period);

        $this->assertCount(1, Settings::all());
        $this->assertInstanceOf(CarbonPeriod::class, Settings::get('k'));
        $this->assertSame($start->format('Y-m-d'), Settings::get('k')->getStartDate()->format('Y-m-d'));
        $this->assertSame($end->format('Y-m-d'), Settings::get('k')->getEndDate()->format('Y-m-d'));
    }
}
