<?php

class Extend_users_for_api
{
    public function up()
    {
        $db = lava_instance()->db;
        $columns = array_column($db->raw('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_ASSOC), 'Field');
        $additions = [
            'firstname' => "VARCHAR(100) NOT NULL DEFAULT ''", 'lastname' => "VARCHAR(100) NOT NULL DEFAULT ''",
            'password' => 'VARCHAR(255) NULL', 'role' => "VARCHAR(20) NOT NULL DEFAULT 'user'",
            'is_active' => 'TINYINT NOT NULL DEFAULT 1', 'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL',
        ];
        foreach ($additions as $name => $definition) {
            if (!in_array($name, $columns, true)) $db->raw("ALTER TABLE users ADD COLUMN `{$name}` {$definition}");
        }
    }
    public function down() { /* Preserve the existing directory and authentication data. */ }
}
