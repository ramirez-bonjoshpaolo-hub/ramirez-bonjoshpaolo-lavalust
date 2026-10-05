<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// The updated Api library expects this global helper. Keep it here for
// compatibility with the older framework checkout without changing Api.php.
if (!function_exists('handle_cors')) {
    function handle_cors()
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowed_origins = config_item('allow_origin');
        $allowed = is_array($allowed_origins)
            ? in_array($origin, $allowed_origins, true)
            : $allowed_origins === '*' || $allowed_origins === $origin;

        header('Vary: Origin');
        header('Content-Type: application/json; charset=UTF-8');
        if ($allowed && $origin !== '') {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Credentials: true');
        } elseif ($allowed_origins === '*') {
            header('Access-Control-Allow-Origin: *');
        }
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 3600');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
