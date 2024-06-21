<?php

namespace App\Enums;

enum TokenAbility: string
{
    case ACCESS_TOKEN = 'access-token';
    case REFRESH_TOKEN = 'refresh-token';
}