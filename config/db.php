<?php

/**
 * Configuración de la base de datos según el entorno
 */
class DatabaseConfig
{
    // Variable para forzar el entorno
    private static $forceEnvironment = null;

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
     * Permite forzar un entorno específico
     * @param string $env
     */
    public static function setEnvironment($env)
    {
        if (in_array($env, ['local', 'production'])) {
            self::$forceEnvironment = $env;
        }
    }

    /**
     * Detecta automáticamente el entorno basado en múltiples factores
     * @return string
     */
    private static function detectEnvironment()
    {
        // Si hay un entorno forzado, úsalo
        if (self::$forceEnvironment !== null) {
            return self::$forceEnvironment;
        }

        // Si existe la constante de configuración, úsala
        if (defined('DB_ENVIRONMENT')) {
            return DB_ENVIRONMENT;
        }

        $hostname = gethostname();
        $serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
        $serverAddr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
        $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        $isLocal = (
            in_array($hostname, ['localhost', '127.0.0.1']) ||
            strpos($hostname, 'local') !== false ||
            strpos($hostname, 'DESKTOP') !== false ||
            strpos($hostname, '.local') !== false ||
            $serverName === 'localhost' ||
            $serverAddr === '127.0.0.1' ||
            substr($remoteAddr, 0, 4) === '127.' ||
            substr($remoteAddr, 0, 3) === '::1' ||
            file_exists(__DIR__ . '/local-environment')  // Archivo opcional para forzar entorno local
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

            try {
                $conexion = mysqli_connect(
                    $config['host'],
                    $config['username'],
                    $config['password'],
                    $config['dbname']
                );

                if (!$conexion) {
                    throw new Exception("Error de conexión: " . mysqli_connect_error());
                }

                mysqli_set_charset($conexion, $config['charset']);
                
                // Configurar el modo estricto de MySQL
                mysqli_query($conexion, "SET sql_mode = 'STRICT_ALL_TABLES'");
                
            } catch (Exception $e) {
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                throw new Exception("No se pudo conectar a la base de datos");
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