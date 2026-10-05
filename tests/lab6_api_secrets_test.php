<?php
define('PREVENT_DIRECT_ACCESS', true);
define('ROOT_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
$config = [];
function config_item($name) { return $GLOBALS['config'][$name] ?? null; }
function show_error($message) { throw new RuntimeException($message); }
function handle_cors() {}
function lava_instance() { return $GLOBALS['fixture']; }
class ApiConfigFixture {
    public function load($name) {
        global $config;
        require ROOT_DIR . 'app/config/api.php';
    }
}
class ApiLoaderFixture { public function library($name) {} }
class ApiDbFixture {
    public function raw($query, $params) { return new ApiStatementFixture(); }
}
class ApiStatementFixture {
    public function fetch($mode) { return ['id' => 1, 'role' => 'user']; }
}
$fixture = (object) ['config' => new ApiConfigFixture(), 'call' => new ApiLoaderFixture(), 'db' => new ApiDbFixture()];
require ROOT_DIR . 'scheme/libraries/Api.php';
function check($condition, $message) {
    if (!$condition) { fwrite(STDERR, $message . PHP_EOL); exit(1); }
}
function rejects($jwt, $refresh) {
    putenv('JWT_SECRET=' . $jwt);
    putenv('REFRESH_TOKEN_KEY=' . $refresh);
    try { new Api(); } catch (RuntimeException $e) { return true; }
    return false;
}
$jwt = bin2hex(random_bytes(32));
$refresh = bin2hex(random_bytes(32));
check(rejects('', $refresh), 'Missing signing key must fail closed.');
check(rejects('short', $refresh), 'Short signing key must fail closed.');
check(rejects($jwt, ''), 'Missing refresh key must fail closed.');
check(rejects($jwt, $jwt), 'Signing and refresh keys must be different.');
check(rejects(str_repeat('a', 64), $refresh), 'Low-entropy keys must be rejected.');
putenv('JWT_SECRET=' . $jwt);
putenv('REFRESH_TOKEN_KEY=' . $refresh);
$api = new Api();
$access = $api->encode_jwt(['sub' => 1, 'type' => 'access', 'role' => 'admin', 'scopes' => ['delete']]);
$refreshToken = $api->encode_jwt(['sub' => 1, 'type' => 'refresh']);
check($api->validate_jwt($access) !== null, 'Valid access token must pass.');
check($api->validate_jwt($refreshToken) === null, 'Refresh token cannot authenticate an access endpoint.');
check($api->validate_jwt($access, 'refresh') === null, 'Access token cannot refresh a session.');
check($api->validate_jwt($refreshToken, 'refresh') !== null, 'Typed refresh validation must pass.');
check($api->validate_jwt($api->encode_jwt(['sub' => 1, 'exp' => time() - 1])) === null, 'Expired tokens must be rejected.');
$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $access;
$claims = $api->require_jwt();
check($claims['role'] === 'user' && $claims['scopes'] === ['read'], 'Database role must override the signed claim.');
echo 'PASS: API key validation, access/refresh separation, expiry, and database role verification.' . PHP_EOL;
