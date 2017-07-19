<?php namespace Kattatzu\Sbif\Facades;

use Illuminate\Support\Facades\Facade;
use Kattatzu\Sbif\Sbif;

class SbifFacade extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return Sbif::class;
    }
}
