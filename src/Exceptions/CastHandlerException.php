<?php

namespace Smartisan\Settings\Exceptions;

class CastHandlerException extends \Exception
{
    /**
     * Create invalid cast handler exception instance.
     *
     * @param string $handler
     * @return \Smartisan\Settings\Exceptions\CastHandlerException
     */
    public static function invalid($handler)
    {
        return new self("Cast handler $handler is invalid. Make sure the cast handler implements Castable interface.");
    }

    /**
     * Create missing cast handler exception instance.
     *
     * @param string $castType
     * @return \Smartisan\Settings\Exceptions\CastHandlerException
     */
    public static function missing($castType)
    {
        return new self("Cast handler for $castType is missing. Make sure to set the handler in settings config file.");
    }
}
