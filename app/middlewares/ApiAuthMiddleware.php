<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiAuthMiddleware
{
    public function handle(Closure $next)
    {
        $lava = lava_instance();
        $lava->call->library('LabApi');
        $lava->LabApi->require_jwt();
        $lava->call->database();
        $lava->LabApi->authenticated_user();
        $lava->LabApi->rate_limit();
        return $next();
    }
}
