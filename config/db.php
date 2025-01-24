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
     * Detecta automáticamente el entorno basado en múltiples métodos
     * @return string
     */
    private static function detectEnvironment()
    {
        // Múltiples formas de detectar el entorno local
        $isLocal = (
            php_sapi_name() === 'cli' || // Entorno de línea de comandos
            (isset($_SERVER['SERVER_NAME']) && 
                (
                    $_SERVER['SERVER_NAME'] === 'localhost' || 
                    $_SERVER['SERVER_NAME'] === '127.0.0.1' ||
                    strpos($_SERVER['SERVER_NAME'], '.local') !== false ||
                    strpos($_SERVER['SERVER_NAME'], 'dev') !== false
                )
            ) ||
            gethostname() === 'localhost' ||
            gethostname() === '127.0.0.1'
        );

        return $isLocal ? 'local' : 'production';
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

            $conexion = mysqli_connect(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['dbname']
            );

            if (!$conexion) {
                error_log("Error de conexión: " . mysqli_connect_error());
                throw new Exception("No se pudo conectar a la base de datos");
            }

            mysqli_set_charset($conexion, $config['charset']);
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