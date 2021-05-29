<?php

namespace Smartisan\Settings\Tests;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Settings;
use Smartisan\Settings\Tests\Models\User;

class SettingsTest extends TestCase
{
    public function test_it_can_set_and_read_single_and_simple_entry()
    {
        $this->assertCount(0, Settings::all());

        Settings::set('k1', 'v1');

        $this->assertCount(1, Settings::all());
        $this->assertSame('v1', Settings::get('k1'));
    }

    public function test_it_can_set_and_read_single_and_castable_entry()
    {
        $date = Carbon::create(2021, 5, 1);

        $this->assertCount(0, Settings::all());

        Settings::set('k1', $date);

        $this->assertCount(1, Settings::all());
        $this->assertInstanceOf(Carbon::class, Settings::get('k1'));
        $this->assertSame($date->format('Y-m-d'), Settings::get('k1')->format('Y-m-d'));
    }

    public function test_it_can_set_and_read_multiple_simple_entries()
    {
        $this->assertCount(0, Settings::all());

        Settings::set([
            'k1' => 'v1',
            'k2' => 10,
            'k3' => [1, 2, 3],
        ]);

        $this->assertCount(3, Settings::all());
        $this->assertSame('v1', Settings::get('k1'));
        $this->assertSame(10, Settings::get('k2'));
        $this->assertSame([1, 2, 3], Settings::get('k3'));
        $this->assertSame(['k1' => 'v1', 'k2' => 10], Settings::get(['k1', 'k2']));
    }

    public function test_it_can_set_and_read_multiple_nested_simple_entries()
    {
        $this->assertCount(0, Settings::all());

        Settings::set([
            'k1' => 'v1',
            'k2' => 10,
            'k3' => [
                'k4' => [1, 2, 3],
                'k5' => [
                    'k6' => [4, 5, 6],
                ],
            ],
        ]);

        $this->assertCount(3, Settings::all());
        $this->assertSame([1, 2, 3], Arr::get(Settings::get('k3'), 'k4'));
        $this->assertSame([4, 5, 6], Arr::get(Settings::get('k3'), 'k5.k6'));
    }

    public function test_it_can_set_and_read_multiple_nested_castable_entries()
    {
        $date = Carbon::create(2021, 5, 1);
        $period = CarbonPeriod::create($date, $date);

        $this->assertCount(0, Settings::all());

        Settings::set([
            'k1' => $date,
            'k2' => [
                'k3' => [
                    'period' => $period,
                ],
            ],
        ]);

        $this->assertCount(2, Settings::all());
        $this->assertInstanceOf(Carbon::class, Settings::get('k1'));
        $this->assertSame($date->format('Y-m-d'), Settings::get('k1')->format('Y-m-d'));
        $this->assertInstanceOf(CarbonPeriod::class, Arr::get(Settings::get('k2'), 'k3.period'));
        $this->assertSame($period->getStartDate()->format('Y-m-d'), Arr::get(Settings::get('k2'), 'k3.period')->getStartDate()->format('Y-m-d'));
        $this->assertSame($period->getEndDate()->format('Y-m-d'), Arr::get(Settings::get('k2'), 'k3.period')->getEndDate()->format('Y-m-d'));
    }

    public function test_it_retrives_default_value()
    {
        $this->assertCount(0, Settings::all());

        Settings::set(['k1' => 'v1', 'k2' => 'v2']);

        $this->assertCount(2, Settings::all());

        $this->assertNull(Settings::get('k3'));
        $this->assertSame('default value', Settings::get('k3', 'default value'));

        $this->assertSame(
            [
            'k1' => 'v1', 'k2' => 'v2', 'k3' => 'default value', 'k4' => 'default value', ],
            Settings::get(['k1', 'k2', 'k3', 'k4'], 'default value')
        );
    }

    public function test_it_can_set_and_read_model_specific_settings()
    {
        $user1 = User::create(['name' => 'Mohammed']);
        $user2 = User::create(['name' => 'Fatema']);

        $this->assertCount(0, Settings::all());

        Settings::set(['k1' => 'v1', 'k2' => 'v2']);

        $this->assertCount(2, Settings::all());

        Settings::for($user1)->set(['k3' => 'v3']);
        Settings::for($user2)->set(['k4' => 'v4', 'k5' => 'v5']);

        $this->assertCount(2, Settings::all());

        $this->assertCount(1, Settings::for($user1)->all());
        $this->assertSame(['k3' => 'v3'], Settings::for($user1)->all());
        $this->assertSame('v3', Settings::for($user1)->get('k3'));

        $this->assertCount(2, Settings::for($user2)->all());
        $this->assertSame(['k4' => 'v4', 'k5' => 'v5'], Settings::for($user2)->all());
        $this->assertSame('v4', Settings::for($user2)->get('k4'));
    }

    public function test_it_can_set_and_read_settings_entries_with_group()
    {
        $this->assertCount(0, Settings::all());

        Settings::group('g1')->set(['k1' => 'v1', 'k2' => 'v2']);
        Settings::group('g2')->set(['k1' => 'v1']);

        $this->assertCount(0, Settings::all());

        $this->assertCount(2, Settings::group('g1')->all());
        $this->assertSame(['k1' => 'v1', 'k2' => 'v2'], Settings::group('g1')->all());
        $this->assertSame('v2', Settings::group('g1')->get('k2'));

        $this->assertCount(1, Settings::group('g2')->all());
        $this->assertSame(['k1' => 'v1'], Settings::group('g2')->all());
        $this->assertSame('v1', Settings::group('g2')->get('k1'));

        $user = User::create(['name' => 'Mohammed']);
        Settings::group('g1')->for($user)->set(['k1' => 'v1', 'k2' => 'v2']);
        $this->assertCount(0, Settings::all());
        $this->assertSame(['k1' => 'v1'], Settings::for($user)->group('g1')->except('k2')->all());
    }

    public function test_it_can_check_for_settings_entry_existance()
    {
        $this->assertCount(0, Settings::all());

        Settings::set(['k1' => 'v1', 'k2' => 'v2']);
        $this->assertTrue(Settings::exists('k1'));
        $this->assertTrue(Settings::exists('k2'));
        $this->assertFalse(Settings::exists('k3'));

        Settings::group('g1')->set(['k3' => 'v3']);
        $this->assertTrue(Settings::group('g1')->exists('k3'));
        $this->assertFalse(Settings::exists('k3'));

        $user = User::create(['name' => 'Mohammed']);

        Settings::for($user)->group('g2')->set(['k1' => 'val', 'k2' => 'val2']);
        $this->assertTrue(Settings::for($user)->group('g2')->exists('k1'));
        $this->assertTrue(Settings::for($user)->group('g2')->exists('k2'));
        $this->assertFalse(Settings::for($user)->group('g2')->exists('k3'));
    }

    public function test_it_can_forget_settings_entries()
    {
        $this->assertCount(0, Settings::all());

        Settings::set(['k1' => 'v1', 'k2' => 'v2']);

        $this->assertCount(2, Settings::all());
        Settings::forget('k1');
        $this->assertCount(1, Settings::all());
        $this->assertSame(['k2' => 'v2'], Settings::all());
        Settings::forget('k2');
        $this->assertCount(0, Settings::all());
    }

    public function test_it_can_forget_model_specific_settings_entry()
    {
        $user = User::create(['name' => 'Mohammed']);

        Settings::set(['k1' => 'v1', 'k2' => 'v2']);

        $this->assertCount(2, Settings::all());

        Settings::for($user)->set('k1', 'v1');
        Settings::for($user)->set('k2', 'v2');

        $this->assertCount(2, Settings::for($user)->all());

        Settings::for($user)->forget('k2');
        $this->assertCount(1, Settings::for($user)->all());
        $this->assertCount(2, Settings::all());
        $this->assertSame(['k1' => 'v1'], Settings::for($user)->all());
    }

    public function test_it_can_forget_specific_settings_entry()
    {
        $user1 = User::create(['name' => 'Mohammed']);
        $user2 = User::create(['name' => 'Fatema']);

        Settings::set('k1', 'v1');
        Settings::for($user1)->group('g1')->set('k1', 'v1');
        Settings::for($user2)->group('g2')->set('k1', 'v1');

        $this->assertCount(1, Settings::all());
        $this->assertCount(1, Settings::for($user1)->group('g1')->all());
        $this->assertCount(1, Settings::for($user2)->group('g2')->all());

        Settings::forget('k1');
        $this->assertCount(0, Settings::all());
        $this->assertCount(1, Settings::for($user1)->group('g1')->all());
        $this->assertCount(1, Settings::for($user2)->group('g2')->all());

        Settings::for($user1)->group('g1')->forget('k1');
        $this->assertCount(0, Settings::for($user1)->group('g1')->all());
        $this->assertCount(1, Settings::for($user2)->group('g2')->all());
    }

    public function test_it_resolves_the_correct_cache_key()
    {
        $this->assertSame('settings.keys=k1&group=&excepts=&for=', Settings::resolveCacheKey('k1'));

        $this->assertSame('settings.keys=k1,k2,k3&group=&excepts=&for=', Settings::resolveCacheKey(['k1', 'k2', 'k3']));

        $user = User::create(['name' => 'Mohammed']);

        $this->assertSame(
            'settings.keys=k1&group=g1&excepts=k3,k4&for=Smartisan\Settings\Tests\Models\User',
            Settings::group('g1')->for($user)->except('k3', 'k4')->resolveCacheKey('k1')
        );
    }
}
