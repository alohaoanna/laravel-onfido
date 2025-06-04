<?php

namespace OANNA\Onfido\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \OANNA\Onfido\OnfidoManager
 */
class Onfido extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \OANNA\Onfido\OnfidoManager::class;
    }
}
