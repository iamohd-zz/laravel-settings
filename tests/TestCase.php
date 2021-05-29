<?php

namespace Smartisan\Settings\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Smartisan\Settings\Facades\Settings;
use Smartisan\Settings\SettingsServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setupDatabase();
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            SettingsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Settings' => Settings::class,
        ];
    }

    protected function setupDatabase()
    {
        $settingsMigration = include __DIR__ . '/../database/migrations/create_settings_table.php.stub';
        $usersMigration = include __DIR__ . '/Migrations/create_users_table.php';

        $settingsMigration->up();
        $usersMigration->up();
    }
}
