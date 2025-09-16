<?php

namespace App\AuditResolvers;

use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

class UserNameResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        if (Auth::getPayload()->get('user')) {
            return Auth::getPayload()->get('user');
        }

        return null;
    }
}
