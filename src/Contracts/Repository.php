<?php

namespace Smartisan\Settings\Contracts;

interface Repository
{
    /**
     * Retrieve settings entry for the given key.
     * The configured values of entry filter will be used to filter the settings entries.
     *
     * @param string|array $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Store settings entry for the given key.
     * The configured values of entry filter will be used to filter the settings entries.
     *
     * @param string|array $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value = null);

    /**
     * Destroy the settings entry for the given key.
     *
     * @param string|array $key
     * @return bool
     */
    public function forget($key);

    /**
     * Retrieve all settings entry.
     * The configured values of entry filter will be used to filter the settings entries.
     *
     * @return array
     */
    public function all();
}
