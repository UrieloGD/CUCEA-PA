<?php
// Configuración para mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
require './config/db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
echo "Inicio del script<br>";

// Consulta SQL para obtener el contenido del archivo
$sql = "SELECT Nombre_Archivo_Dep, Ruta_Archivo_Dep FROM Plantilla_Dep WHERE ID_Archivo_Dep = ?";
$stmt = $conexion->prepare($sql);
$id_archivo = 1; // Reemplaza con el ID del archivo que deseas procesar
$stmt->bind_param("i", $id_archivo);
$stmt->execute();
$resultado = $stmt->get_result();

echo "Resultado de la consulta: " . $resultado->num_rows . " filas<br>";

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $nombreArchivo = $fila['Nombre_Archivo_Dep'];
    $contenidoArchivo = $fila['Ruta_Archivo_Dep'];

    echo "Nombre del archivo: " . $nombreArchivo . "<br>";

    // Crear un archivo temporal con el contenido del archivo
    $rutaArchivoTemporal = sys_get_temp_dir() . '/' . $nombreArchivo;
    file_put_contents($rutaArchivoTemporal, $contenidoArchivo);

    echo "Archivo temporal creado: " . $rutaArchivoTemporal . "<br>";

    // Cargar el archivo desde la ruta temporal
    $documento = IOFactory::load($rutaArchivoTemporal);
    $hojaActual = $documento->getActiveSheet();

    echo "Archivo cargado exitosamente<br>";

    // Obtener el número de filas y columnas de la hoja de cálculo
    $numeroFilas = $hojaActual->getHighestRow();
    $numeroColumnas = $hojaActual->getHighestColumn();
    
    echo "Número de filas en la hoja de cálculo: " . $numeroFilas . "<br>";
    echo "Número de columnas en la hoja de cálculo: " . $numeroColumnas . "<br>";
    
    echo "Archivo cargado exitosamente<br>";

    // Imprimir el contenido de la hoja de cálculo
    $fila = 1;
    $columna = 'A';
    $ultimaFilaConDatos = 1;
    $ultimaColumnaConDatos = 'A';

    echo "Contenido de la hoja de cálculo:<br>";
    while (true) {
        $valor = $hojaActual->getCell($columna . $fila)->getValue();
        if ($valor !== null) {
            echo $valor . "\t";
            $ultimaFilaConDatos = $fila;
            $ultimaColumnaConDatos = $columna;
        } else {
            echo "-\t";
        }

        $columna++;
        if ($columna === 'O') { // Ajusta este valor según el número máximo de columnas en tu archivo
            $columna = 'A';
            $fila++;
        }

        if ($fila > 1000) { // Ajusta este valor según el número máximo de filas en tu archivo
            break;
        }
    }

    echo "<br>Última fila con datos: " . $ultimaFilaConDatos . "<br>";
    echo "Última columna con datos: " . $ultimaColumnaConDatos . "<br>";

    // Obtener el encabezado (primera fila)
    $encabezado = [];
    $letra = $ultimaColumnaConDatos;
    $numeroLetra = Coordinate::columnIndexFromString($letra);
    for ($indiceColumna = 1; $indiceColumna <= $numeroLetra; $indiceColumna++) {
        $columna = Coordinate::stringFromColumnIndex($indiceColumna);
        $encabezado[] = $hojaActual->getCell($columna . '1')->getValue();
    }

    // Iterar sobre las filas del archivo Excel y almacenar los datos en la otra tabla
    for ($indiceFila = 2; $indiceFila <= $ultimaFilaConDatos; $indiceFila++) {
        $valores = [];
        foreach ($encabezado as $indiceColumna => $nombreColumna) {
            $columna = Coordinate::stringFromColumnIndex($indiceColumna + 1);
            $valorCelda = $hojaActual->getCell($columna . $indiceFila)->getValue();
            $valores[] = "'" . $valorCelda . "'";
            echo "Valor de la celda (" . $columna . $indiceFila . "): " . $valorCelda . "<br>";
        }

        $valoresStr = implode(", ", $valores);
        $columnas = implode(", ", array_map(function ($col) {
            return "`$col`";
        }, $encabezado));

        echo "Consulta SQL: INSERT INTO Data_finanzas ($columnas) VALUES ($valoresStr);<br>";

        // Ejecutar la consulta SQL para insertar la fila en la otra tabla
        $sql = "INSERT INTO Data_finanzas ($columnas) VALUES ($valoresStr)";
        if ($conexion->query($sql) === FALSE) {
            echo "Error al insertar datos: " . $conexion->error . "<br>";
        } else {
            echo "Datos insertados correctamente en Data_finanzas.<br>";
        }
    }

    // Eliminar el archivo temporal
    unlink($rutaArchivoTemporal);
} else {
    echo "No se encontró el archivo en la base de datos.";
}
?>