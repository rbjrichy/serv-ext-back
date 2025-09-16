<?php

namespace App\AuditResolvers;

use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

class UserRolResolver implements Resolver
{

    public static function resolve(Auditable $auditable )
    {
        if (Auth::getPayload()->get('rol')) {
            return Auth::getPayload()->get('rol');
        }

        return null;
    }
}
