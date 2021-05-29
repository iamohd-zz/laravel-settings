<?php

namespace Smartisan\Settings\Tests\Casts;

use Smartisan\Settings\Contracts\Castable;

class DummyCast implements Castable
{
    public function set($payload)
    {
        return 'dummy value';
    }

    public function get($payload)
    {
        return 'dummy value';
    }
}
