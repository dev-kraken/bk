<?php

namespace App\Facades;

use App\Models\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static User register(array $data)
 */
class AuthServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'authFacade';
    }
}