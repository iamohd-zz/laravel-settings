<?php

namespace Smartisan\Settings\Contracts;

interface Castable
{
    /**
     * Apply casting rules when storing the payload into the settings repository.
     *
     * @param mixed $payload
     * @return mixed
     */
    public function set($payload);

    /**
     * Apply casting rules when retrieving the payload from the settings repository.
     *
     * @param mixed $payload
     * @return mixed
     */
    public function get($payload);
}
