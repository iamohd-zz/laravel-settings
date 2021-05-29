<?php

if (! function_exists('settings')) {
    /**
     * Get a settings manager instance.
     *
     * @return \Smartisan\Settings\Settings
     */
    function settings()
    {
        return app('settings');
    }
}
