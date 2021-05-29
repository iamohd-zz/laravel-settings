<?php

namespace Smartisan\Settings\Tests;

use Smartisan\Settings\Settings;

class HelpersTest extends TestCase
{
    public function test_settings_helper_retrieves_an_instance_of_settings_class()
    {
        $this->assertInstanceOf(Settings::class, settings());
    }
}
