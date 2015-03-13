<?php namespace Hasan\DeskApi;

use Illuminate\Support\Facades\Facade;

class DeskApiFacade extends Facade{
    protected static function getFacadeAccessor() { return 'DeskApi'; }
}