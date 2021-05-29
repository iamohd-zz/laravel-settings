<?php

namespace Smartisan\Settings\Tests\Casts;

use Smartisan\Settings\Contracts\Castable;

class AnotherCast implements Castable
{
    protected $param;

    public function __construct($param)
    {
        $this->param = $param;
    }

    public function set($payload)
    {
        if ($this->param === 't1') {
            return 'v1';
        }

        return 'v2';
    }

    public function get($payload)
    {
        if ($this->param === 't1') {
            return 'evaluated 1';
        }

        return 'evaluated 2';
    }
}
