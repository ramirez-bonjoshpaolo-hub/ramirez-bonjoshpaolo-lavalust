<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class MigrationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (PHP_SAPI !== 'cli') { show_404(); exit; }
        $this->call->library('migration');
    }
    public function create_migration($migration_class)
    {
        if (!preg_match('/^[a-z][a-z0-9_]*$/D', $migration_class)) {
            fwrite(STDERR, "Use a snake_case migration name.\n"); exit(1);
        }
        $this->migration->create_migration($migration_class);
    }
    public function migrate() { $this->migration->migrate(); }
    public function rollback() { $this->migration->rollback(); }
    public function rollback_all() { $this->migration->rollback_all(); }
    public function refresh() { $this->migration->refresh(); }
    public function status() { $this->migration->status(); }
}
