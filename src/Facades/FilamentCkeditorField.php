<?php

namespace kahusoftware\FilamentCkeditorField\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \kahusoftware\FilamentCkeditorField\FilamentCkeditorField
 */
class FilamentCkeditorField extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \kahusoftware\FilamentCkeditorField\FilamentCkeditorField::class;
    }
}
