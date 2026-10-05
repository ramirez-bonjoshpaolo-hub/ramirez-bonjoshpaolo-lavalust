<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
require_once SYSTEM_DIR . 'libraries/Api.php';

// Keep LavaLust's response, CORS, JWT signing, and rate-limit helpers.
class LabApi extends Api
{
    public function __construct()
    {
        if (strlen(getenv('JWT_SECRET') ?: getenv('APP_KEY') ?: '') < 32) {
            http_response_code(503);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'API authentication is not configured.']);
            exit;
        }
        parent::__construct();
        header('Cache-Control: no-store');
        header('Vary: Origin');
    }

    public function json_body(): array
    {
        if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === false) {
            $this->respond_error('Send an application/json request body.', 415);
        }
        try {
            $body = json_decode(file_get_contents('php://input'), true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->respond_error('Invalid JSON.', 400);
        }
        if (!is_array($body) || array_is_list($body) && $body !== []) {
            $this->respond_error('Expected a JSON object.', 400);
        }
        return $body;
    }

    public function issue_pair(array $user): array
    {
        $db = lava_instance()->db;
        $sid = bin2hex(random_bytes(24));
        $access = $this->encode_jwt(['sub' => (int) $user['id'], 'type' => 'access', 'sid' => $sid]);
        $refresh = $this->encode_jwt([
            'sub' => (int) $user['id'], 'type' => 'refresh', 'jti' => $sid,
            'exp' => time() + $this->refresh_token_expiration,
        ]);
        $db->raw('DELETE FROM refresh_tokens WHERE expires_at <= CURRENT_TIMESTAMP');
        $db->raw('INSERT INTO refresh_tokens (user_id, token, expires_at, jti) VALUES (?, ?, ?, ?)', [
            $user['id'], $this->refresh_hash($refresh),
            gmdate('Y-m-d H:i:s', time() + $this->refresh_token_expiration), $sid,
        ]);
        return ['access_token' => $access, 'refresh_token' => $refresh,
            'expires_in' => $this->payload_token_expiration, 'token_type' => 'Bearer'];
    }

    public function authenticated_user(): array
    {
        $claims = $this->require_jwt();
        if (($claims['type'] ?? '') !== 'access' || empty($claims['sid'])) {
            $this->respond_error('An access token is required.', 401);
        }
        $user = lava_instance()->db->raw(
            'SELECT u.id, u.username, u.email FROM users u JOIN refresh_tokens r ON r.user_id = u.id
             WHERE u.id = ? AND u.is_active = 1 AND r.jti = ? AND r.expires_at > CURRENT_TIMESTAMP LIMIT 1',
            [$claims['sub'], $claims['sid']]
        )->fetch(PDO::FETCH_ASSOC);
        if (!$user) $this->respond_error('Your session has expired. Please sign in again.', 401);
        return $user;
    }

    public function refresh_user(string $token): array
    {
        $claims = $this->validate_jwt($token);
        if (!$claims || ($claims['type'] ?? '') !== 'refresh') {
            $this->respond_error('Invalid or expired refresh token.', 401);
        }
        $user = lava_instance()->db->raw(
            'SELECT u.id, u.username, u.email FROM users u JOIN refresh_tokens r ON r.user_id = u.id
             WHERE u.id = ? AND u.is_active = 1 AND r.token = ? AND r.expires_at > CURRENT_TIMESTAMP LIMIT 1',
            [$claims['sub'], $this->refresh_hash($token)]
        )->fetch(PDO::FETCH_ASSOC);
        if (!$user) $this->respond_error('Your session has expired. Please sign in again.', 401);
        return $user;
    }

    public function revoke(string $token): void
    {
        lava_instance()->db->raw('DELETE FROM refresh_tokens WHERE token = ?', [$this->refresh_hash($token)]);
    }

    private function refresh_hash(string $token): string
    {
        return hash_hmac('sha256', $token, config_item('refresh_token_key'));
    }
}
