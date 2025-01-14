<?php

/**
 * Configuración de la base de datos según el entorno
 */
class DatabaseConfig
{
    // Configuraciones para diferentes entornos
    private static $configs = [
        'local' => [
            'host' => 'localhost',
            'dbname' => 'pa',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8'
        ],
        'production' => [
            'host' => 'localhost',
            'dbname' => 'pa',
            'username' => 'pa',
            'password' => 'hyXbFnAYRH63yU',
            'charset' => 'utf8'
        ]
    ];

    /**
     * Detecta automáticamente el entorno basado en múltiples factores
     * @return string
     */
    private static function detectEnvironment()
    {
        // 1. Verificar por archivo de configuración local
        if (file_exists(__DIR__ . '/local_environment')) {
            return 'local';
        }

        // 2. Verificar el hostname
        $hostname = gethostname();
        if (in_array($hostname, ['localhost', '127.0.0.1'])) {
            return 'local';
        }

        // 3. Verificar la IP del servidor
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
        if (in_array($serverAddr, ['127.0.0.1', '::1'])) {
            return 'local';
        }

        // 4. Verificar el nombre del servidor
        $serverName = $_SERVER['SERVER_NAME'] ?? '';
        if (strpos($serverName, 'localhost') !== false) {
            return 'local';
        }

        // Si no se cumple ninguna condición, asumimos que es producción
        return 'production';
    }

    /**
     * Obtiene una conexión a la base de datos
     * @return mysqli
     * @throws Exception
     */
    public static function getConnection()
    {
        static $conexion = null;

        if ($conexion === null) {
            $env = self::detectEnvironment();
            $config = self::$configs[$env];

            try {
                $conexion = mysqli_connect(
                    $config['host'],
                    $config['username'],
                    $config['password'],
                    $config['dbname']
                );

                if (!$conexion) {
                    throw new Exception(mysqli_connect_error());
                }

                mysqli_set_charset($conexion, $config['charset']);
            } catch (Exception $e) {
                error_log("Error de conexión en entorno '$env': " . $e->getMessage());
                throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return $conexion;
    }
}

// Función global para mantener compatibilidad con el código existente
if (!function_exists('getConnection')) {
    function getConnection()
    {
        return DatabaseConfig::getConnection();
    }
}

// Establecer la conexión global
try {
    $conexion = getConnection();
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}
