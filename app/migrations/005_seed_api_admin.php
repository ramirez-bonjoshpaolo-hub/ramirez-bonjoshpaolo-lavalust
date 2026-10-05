<?php

class Seed_api_admin
{
    public function up()
    {
        $password = getenv('AUTH_PASSWORD') ?: '';
        if ($password === '') throw new RuntimeException('Set AUTH_PASSWORD before running the admin migration.');
        $db = lava_instance()->db;
        $username = getenv('AUTH_USERNAME') ?: 'admin';
        $user = $db->raw('SELECT id, password FROM users WHERE username = ? LIMIT 1', [$username])->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if (!$user['password']) $db->raw('UPDATE users SET password = ?, role = ?, is_active = 1 WHERE id = ?',
                [password_hash($password, PASSWORD_DEFAULT), 'admin', $user['id']]);
            return;
        }
        $db->raw('INSERT INTO users (firstname, lastname, username, email, password, role) VALUES (?, ?, ?, ?, ?, ?)', [
            'Bon Josh Paolo D.', 'Ramirez', $username,
            getenv('AUTH_EMAIL') ?: 'lab6-admin@ramirez.local', password_hash($password, PASSWORD_DEFAULT), 'admin',
        ]);
    }
    public function down() { /* Existing users are retained. */ }
}
