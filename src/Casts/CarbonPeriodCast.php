<?php

namespace Smartisan\Settings\Casts;

use Carbon\CarbonPeriod;
use Smartisan\Settings\Contracts\Castable;

class CarbonPeriodCast implements Castable
{
    /**
     * Apply casting rules when storing the payload into the settings repository.
     *
     * @param \Carbon\CarbonPeriod $payload
     * @return array
     */
    public function set($payload)
    {
        return [
            'start' => $payload->getStartDate(),
            'end' => $payload->getEndDate(),
        ];
    }

    /**
     * Apply casting rules when storing the payload into the settings repository.
     *
     * @param array $payload
     * @return \Carbon\CarbonPeriod
     */
    public function get($payload)
    {
        return new CarbonPeriod(
            $payload['start'],
            $payload['end']
        );
    }
}
