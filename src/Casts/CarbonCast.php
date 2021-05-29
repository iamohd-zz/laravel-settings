<?php

namespace Smartisan\Settings\Casts;

use Carbon\Carbon;
use Smartisan\Settings\Contracts\Castable;

class CarbonCast implements Castable
{
    /**
     * Apply casting rules when storing the payload into the settings repository.
     *
     * @param \Carbon\Carbon $payload
     * @return string
     */
    public function set($payload)
    {
        return $payload->format(DATE_ATOM);
    }

    /**
     * Apply casting rules when retrieving the payload from the settings repository.
     *
     * @param string $payload
     * @return \Carbon\Carbon
     */
    public function get($payload)
    {
        return new Carbon($payload);
    }
}
