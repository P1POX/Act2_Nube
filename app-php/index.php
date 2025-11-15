<?php
$requiredPort = getenv('APP_REQUIRED_PORT') ?: '8080';
$dbHost = getenv('DB_HOST') ?: 'nube-db';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'empresa';
$dbUser = getenv('DB_USER') ?: 'appuser';
$dbPass = getenv('DB_PASSWORD') ?: 'apppass';

$hostHeader = $_SERVER['HTTP_HOST'] ?? '';
if (strpos($hostHeader, ':') !== false) {
    [, $serverPort] = explode(':', $hostHeader, 2);
} else {
    $serverPort = $_SERVER['SERVER_PORT'] ?? '80';
}

$containerName = gethostname();

$checks = [
    'container_name' => [
        'ok' => ($containerName === 'nube-web'),
        'actual' => $containerName,
        'esperado' => 'nube-web'
    ],
    'host_port' => [
        'ok' => ($serverPort === (string)$requiredPort),
        'actual' => $serverPort,
        'esperado' => (string)$requiredPort
    ],
    'env_db_host' => [
        'ok' => ($dbHost === 'nube-db'),
        'actual' => $dbHost,
        'esperado' => 'nube-db'
    ],
];

$dbStatus = [
    'ok' => false,
    'mensaje' => '',
    'total_clientes' => null,
    'tablas_total' => null,
    'tablas_nombres' => [],
];

try {
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2,
    ]);
    $pdo->query('SELECT 1');

    // Total filas en clientes
    $count = (int)$pdo->query('SELECT COUNT(*) AS c FROM clientes')->fetch()['c'];

    // Cantidad de tablas en el schema
    $stmtCount = $pdo->prepare('SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = :db');
    $stmtCount->execute(['db' => $dbName]);
    $tablasTotal = (int)$stmtCount->fetch()['c'];

    // Nombres de todas las tablas
    $stmtNames = $pdo->prepare('SELECT table_name FROM information_schema.tables WHERE table_schema = :db ORDER BY table_name');
    $stmtNames->execute(['db' => $dbName]);
    $tablasNombres = $stmtNames->fetchAll(PDO::FETCH_COLUMN);

    // Obtener registros de la tabla clientes
    $stmtClientes = $pdo->prepare('SELECT nombre FROM clientes');
    $stmtClientes->execute();
    $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

    $dbStatus = [
        'ok' => true,
        'mensaje' => 'Conexión OK',
        'total_clientes' => $count,
        'tablas_total' => $tablasTotal,
        'tablas_nombres' => $tablasNombres,
    ];
} catch (Throwable $e) {
    $dbStatus = [
        'ok' => false,
        'mensaje' => 'Error de conexión: ' . $e->getMessage(),
        'total_clientes' => null,
        'tablas_total' => null,
        'tablas_nombres' => [],
    ];
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Monitor Docker – Eval 2</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            margin: 2rem;
        }

        .ok {
            color: #167d17;
            font-weight: 600;
        }

        .fail {
            color: #b00020;
            font-weight: 600;
        }

        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 4px;
        }

        table {
            border-collapse: collapse;
            margin-top: 1rem;
        }

        td,
        th {
            border: 1px solid #ddd;
            padding: .5rem .75rem;
            vertical-align: top;
        }

        ul {
            margin: .25rem 0 0 .75rem;
            padding: 0;
        }

        li {
            margin: 0;
        }
    </style>
</head>

<body>
    <h1>Sigue a <a href="https://www.instagram.com/lonkodev" target="_blank">Lonkodev</a></h1>
    
    <table>
        <tr>
            <th>Alumnos</th>
        </tr>
        <?php if ($dbStatus['ok']): 
            foreach ($clientes as $cliente) { ?>
            <tr>
                <td>
                    <?php echo $cliente['nombre'] . "<br>"; ?>
                </td>
            </tr>
            <?php } ?>
        <?php endif; ?>
    </table>
</body>

</html>