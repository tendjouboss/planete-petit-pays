<?php
/**
 * Endpoint de santé pour Railway
 * Vérifie que l'application fonctionne correctement
 */

header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'version' => '1.0.0',
    'checks' => []
];

// Vérifier PHP
$health['checks']['php'] = [
    'status' => 'ok',
    'version' => PHP_VERSION,
    'extensions' => [
        'pdo' => extension_loaded('pdo'),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'mbstring' => extension_loaded('mbstring'),
        'curl' => extension_loaded('curl')
    ]
];

// Vérifier la configuration
try {
    require_once 'includes/config.php';
    $health['checks']['config'] = [
        'status' => 'ok',
        'db_host' => defined('DB_HOST') ? 'configured' : 'missing',
        'db_name' => defined('DB_NAME') ? 'configured' : 'missing',
        'site_url' => defined('SITE_URL') ? 'configured' : 'missing'
    ];
} catch (Exception $e) {
    $health['checks']['config'] = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    $health['status'] = 'unhealthy';
}

// Vérifier la base de données
try {
    if (function_exists('getDBConnection')) {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT 1");
        $health['checks']['database'] = [
            'status' => 'ok',
            'connection' => 'successful'
        ];
    } else {
        $health['checks']['database'] = [
            'status' => 'error',
            'message' => 'getDBConnection function not available'
        ];
        $health['status'] = 'unhealthy';
    }
} catch (Exception $e) {
    $health['checks']['database'] = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    $health['status'] = 'unhealthy';
}

// Vérifier les dossiers uploads
$uploadDirs = ['assets/uploads', 'assets/uploads/audio', 'assets/uploads/video', 'assets/uploads/albums'];
$health['checks']['directories'] = [];

foreach ($uploadDirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        $health['checks']['directories'][$dir] = 'ok';
    } else {
        $health['checks']['directories'][$dir] = 'error';
        $health['status'] = 'unhealthy';
    }
}

// Définir le code de statut HTTP
if ($health['status'] === 'healthy') {
    http_response_code(200);
} else {
    http_response_code(503);
}

echo json_encode($health, JSON_PRETTY_PRINT);
?> 