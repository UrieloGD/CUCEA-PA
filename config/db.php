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
     * Detecta automáticamente el entorno basado en el servidor
     * @return string
     */
    private static function detectEnvironment()
    {
        // Verificar si estamos en el servidor de producción usando la ruta del documento
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if (strpos($documentRoot, '/var/www/html/pa.cucea.udg.mx') !== false) {
            return 'production';
        }

        // Verificar el nombre del host
        $serverName = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
        if (strpos($serverName, 'pa.cucea.udg.mx') !== false) {
            return 'production';
        }

        // Si no se detecta como producción, es local
        return 'local';
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
