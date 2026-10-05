<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiAuthMiddleware
{
    public function handle(Closure $next)
    {
        $lava = lava_instance();
        $lava->call->library('LabApi');
        // Reject invalid tokens before connecting; require_jwt now verifies
        // the database user, so it runs only after the connection is loaded.
        if (!$lava->LabApi->validate_jwt($lava->LabApi->get_bearer_token() ?? '')) {
            $lava->LabApi->respond_error('Unauthorized', 401);
        }
        $lava->call->database();
        $lava->LabApi->authenticated_user();
        $lava->LabApi->rate_limit();
        return $next();
    }
}
