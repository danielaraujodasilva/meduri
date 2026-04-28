<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$localConfigPath = __DIR__ . '/conexao.local.php';
$localConfig = [];

if (file_exists($localConfigPath)) {
    $loaded = require $localConfigPath;
    if (is_array($loaded)) {
        $localConfig = $loaded;
    }
}

$config = array_merge([
    'host' => getenv('MEDURI_DB_HOST') ?: null,
    'port' => getenv('MEDURI_DB_PORT') ?: 3306,
    'database' => getenv('MEDURI_DB_NAME') ?: null,
    'username' => getenv('MEDURI_DB_USER') ?: null,
    'password' => getenv('MEDURI_DB_PASS') ?: null,
], $localConfig);

$missing = [];
foreach (['host', 'database', 'username', 'password'] as $field) {
    if (empty($config[$field])) {
        $missing[] = $field;
    }
}

if ($missing) {
    http_response_code(500);
    echo 'Configuracao do banco incompleta em tools/ficha/config/conexao.php. Campos faltando: ' . implode(', ', $missing) . '.';
    exit;
}

$conn = new mysqli(
    $config['host'],
    $config['username'],
    $config['password'],
    $config['database'],
    (int) $config['port']
);

$conn->set_charset('utf8mb4');
