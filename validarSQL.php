<?php

require 'vendor/autoload.php';
require './config/db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$nombreArchivo = 'Sistemas_Info.xlsx';
$documento = IOFactory::load($nombreArchivo);
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

$errorEncontrado = false; // Bandera para indicar si se encontró algún error
$mensajeError = ''; // Cadena para almacenar el mensaje de error

// Iterar sobre las filas del archivo Excel
for ($indiceFila = 2; $indiceFila <= $numeroFilas; $indiceFila++) {
    $insertarFila = true; // Bandera para determinar si se insertará la fila actual

    // Crear variables dinámicas con los nombres de las columnas del encabezado
    foreach ($encabezado as $indiceColumna => $nombreColumna) {
        $columna = Coordinate::stringFromColumnIndex($indiceColumna);
        $valor = $hojaActual->getCell($columna . $indiceFila)->getValue();

        // Validar el valor según las restricciones individuales
        if ($nombreColumna == 'NRC') {
            // Validar que el valor no tenga más de 6 dígitos
            if (is_numeric($valor) && strlen($valor) > 20) {
                $errorEncontrado = true;
                $insertarFila = false; // No insertar esta fila
                $mensajeError = "Se encontraron errores en la columna $nombreColumna: </br>El valor no debe tener más de 6 dígitos.";
                break 2; // Detener el proceso de inserción y el bucle principal si se encuentra un error
            }
        } elseif ($nombreColumna == 'Columna3') {
            // Validar que el valor sea un número decimal válido
            if (!is_numeric($valor)) {
                $errorEncontrado = true;
                $insertarFila = false; // No insertar esta fila
                $mensajeError = "Se encontraron errores en la columna $nombreColumna: El valor debe ser un número decimal.";
                break 2; // Detener el proceso de inserción y el bucle principal si se encuentra un error
            }
        }
        // Agrega más validaciones según tus necesidades

        ${$nombreColumna} = $valor; // Crear variable dinámica
    }

    // Si se encontró un error, no se realizará ninguna inserción para la fila actual
    if (!$insertarFila) {
        break; // Detener el procesamiento de filas si se encuentra un error
    }

    $sql = "INSERT INTO bd (";
    foreach ($encabezado as $nombreColumna) {
        $sql .= "`$nombreColumna`, ";
    }
    $sql = rtrim($sql, ', ') . ") VALUES (";
    foreach ($encabezado as $nombreColumna) {
        $sql .= "'" . ${$nombreColumna} . "', ";
    }
    $sql = rtrim($sql, ', ') . ")";

    // Ejecutar la consulta SQL
    $conexion->query($sql);
}

if ($errorEncontrado) {
    echo $mensajeError;
} else {
    echo 'La Base de Datos se cargó correctamente';
}
