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
     * Detecta automáticamente el entorno basado en el hostname
     * @return string
     */
    private static function detectEnvironment()
    {
        $hostname = gethostname();
        return (in_array($hostname, ['localhost', '127.0.0.1']) || strpos($hostname, 'local') !== false)
            ? 'local'
            : 'production';
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
