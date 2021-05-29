<?php

namespace Smartisan\Settings;

use Settings;

trait HasSettings
{
    /**
     * Retrieve the settings manager instance for this model.
     *
     * @return \Smartisan\Settings\Settings
     */
    public function settings()
    {
        return Settings::for($this);
    }
}
