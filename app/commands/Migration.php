<?php

class Migration
{
    public static $command = 'migration';
    public static $description = 'Manage database migrations from the CLI';
    public static $arguments = [
        '[action]' => 'run, create-migration, rollback, rollback-all, refresh, status',
        '[name]' => 'Migration name for create-migration',
        '[--confirm]' => 'Required for destructive rollback or refresh operations',
    ];
    public function handle($action = null, array $flags = [], $name = null)
    {
        $map = ['run' => 'migrate', 'create-migration' => 'create-migration', 'rollback' => 'rollback',
            'rollback-all' => 'rollback-all', 'refresh' => 'refresh', 'status' => 'status'];
        $action = $action ?? 'run';
        if (!isset($map[$action])) { fwrite(STDERR, "Unknown migration action.\n"); exit(1); }
        if (in_array($action, ['rollback', 'rollback-all', 'refresh'], true) && empty($flags['confirm'])) {
            fwrite(STDERR, "This command can remove data. Use --confirm on a development database.\n"); exit(1);
        }
        $route = $map[$action];
        if ($action === 'create-migration') {
            $name = $name ?? ($GLOBALS['positional'][1] ?? null);
            if (!$name || !preg_match('/^[a-z][a-z0-9_]*$/D', $name)) {
                fwrite(STDERR, "Example: php lava migration create-migration create_products_table\n"); exit(1);
            }
            $route .= '/' . $name;
        }
        exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(PUBLIC_DIR . 'index.php') . ' ' . escapeshellarg($route), $lines, $code);
        $output = implode(PHP_EOL, $lines);
        // Some framework error pages exit with code 0; fail startup explicitly.
        if (stripos($output, '<!DOCTYPE') !== false || stripos($output, '<html') !== false) {
            fwrite(STDERR, "Migration failed. Verify database host, TLS, credentials, and schema.\n");
            exit(1);
        }
        echo $output . PHP_EOL;
        exit($code);
    }
}
