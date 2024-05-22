<?php

require 'vendor/autoload.php';
require './config/db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["archivo_excel"]["name"]) && $_FILES["archivo_excel"]["error"] == UPLOAD_ERR_OK) {
        $nombreArchivo = $_FILES["archivo_excel"]["name"];
        $rutaArchivo = $_FILES["archivo_excel"]["tmp_name"];
        $tamanoArchivo = $_FILES["archivo_excel"]["size"];

        // Mover el archivo a la carpeta deseada (opcional)
        $directorioDestino = "./archivos/";
        $rutaDestino = $directorioDestino . $nombreArchivo;
        move_uploaded_file($rutaArchivo, $rutaDestino);

        // Guardar información del archivo en la base de datos
        $sqlInsertArchivo = "INSERT INTO archivos (nombre, ruta, tamaño) VALUES ('$nombreArchivo', '$rutaDestino', $tamanoArchivo)";
        $conexion->query($sqlInsertArchivo);

        // Procesar los datos del archivo Excel
        $documento = IOFactory::load($rutaDestino);
        $hojaActual = $documento->getSheet(0);
        $numeroFilas = $hojaActual->getHighestDataRow();
        $letra = $hojaActual->getHighestDataColumn();
        $numeroLetra = Coordinate::columnIndexFromString($letra);

        // Obtener el encabezado (primera fila)
        $encabezado = [];
        for ($indiceColumna = 1; $indiceColumna <= $numeroLetra; $indiceColumna++) {
            $columna = Coordinate::stringFromColumnIndex($indiceColumna);
            $encabezado[$indiceColumna] = $hojaActual->getCell($columna . '1')->getValue();
        }

        // Iterar sobre las filas del archivo Excel
        for ($indiceFila = 2; $indiceFila <= $numeroFilas; $indiceFila++) {
            // Procesar cada fila como lo desees
            // Por ejemplo, aquí podrías insertar los datos en otra tabla de tu base de datos
            // o realizar otras operaciones necesarias.
        }

        // Aquí puedes redirigir o imprimir un mensaje de éxito
        echo "Archivo subido y procesado correctamente.";
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "Acceso denegado.";
}
?>
