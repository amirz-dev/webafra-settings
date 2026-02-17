<?php

namespace Webafra\LaraSetting\Facade;

use Illuminate\Support\Facades\Facade;

class Setting extends Facade
{
    /**
     * Get the registered name of the component in the container.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'webafra-settings';
    }
}
