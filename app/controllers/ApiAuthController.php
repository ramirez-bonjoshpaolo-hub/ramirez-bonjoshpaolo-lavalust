<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiAuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('LabApi');
    }
    public function health()
    {
        $this->LabApi->respond(['status' => 'ok', 'application' => 'Ramirez Lab 6 API']);
    }
    public function preflight() {}
    public function login()
    {
        $this->LabApi->rate_limit('login_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 10, 60);
        $body = $this->LabApi->json_body();
        if (!is_string($body['username'] ?? null) || !is_string($body['password'] ?? null)) {
            $this->LabApi->respond_error('Username and password are required.', 422);
        }
        $this->call->database();
        $user = $this->db->raw('SELECT id, username, email, password FROM users WHERE username = ? AND is_active = 1 LIMIT 1',
            [trim($body['username'])])->fetch(PDO::FETCH_ASSOC);
        if (!$user || empty($user['password']) || !password_verify($body['password'], $user['password'])) {
            $this->LabApi->respond_error('Invalid username or password.', 401);
        }
        $tokens = $this->LabApi->issue_pair($user);
        unset($user['password']);
        $this->LabApi->respond(['user' => $user, 'tokens' => $tokens]);
    }
    public function refresh()
    {
        $this->LabApi->rate_limit();
        $body = $this->LabApi->json_body();
        $token = is_string($body['refresh_token'] ?? null) ? $body['refresh_token'] : '';
        $this->call->database();
        $user = $this->LabApi->refresh_user($token);
        $this->LabApi->revoke($token);
        $this->LabApi->respond(['user' => $user, 'tokens' => $this->LabApi->issue_pair($user)]);
    }
    public function logout()
    {
        $body = $this->LabApi->json_body();
        $this->call->database();
        $this->LabApi->revoke(is_string($body['refresh_token'] ?? null) ? $body['refresh_token'] : '');
        $this->LabApi->respond(['message' => 'You have been logged out.']);
    }
    public function me()
    {
        $this->call->database();
        $this->LabApi->respond(['user' => $this->LabApi->authenticated_user()]);
    }
}
