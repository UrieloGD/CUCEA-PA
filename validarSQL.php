<?php
require 'vendor/autoload.php';
require './config/db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Obtener el nombre del archivo desde la tabla Plantilla_Dep
$sql = "SELECT Nombre_Archivo_Dep, Ruta_Archivo_Dep FROM Plantilla_Dep";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $nombreArchivo = $fila['Ruta_Archivo_Dep'] . '/' . $fila['Nombre_Archivo_Dep'];

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

        $errorEncontrado = false;
        $mensajeError = '';

        // Iterar sobre las filas del archivo Excel
        for ($indiceFila = 2; $indiceFila <= $numeroFilas; $indiceFila++) {
            $insertarFila = true;

            // Crear variables dinámicas con los nombres de las columnas del encabezado
            foreach ($encabezado as $indiceColumna => $nombreColumna) {
                $columna = Coordinate::stringFromColumnIndex($indiceColumna);
                $valor = $hojaActual->getCell($columna . $indiceFila)->getValue();

                // Validar el valor según las restricciones individuales
                if ($nombreColumna == 'NRC') {
                    // Validar que el valor no tenga más de 6 dígitos
                    if (is_numeric($valor) && strlen($valor) > 20) {
                        $errorEncontrado = true;
                        $insertarFila = false;
                        $mensajeError = "Se encontraron errores en la columna $nombreColumna: </br>El valor no debe tener más de 6 dígitos.";
                        break 2;
                    }
                } elseif ($nombreColumna == 'Columna3') {
                    // Validar que el valor sea un número decimal válido
                    if (!is_numeric($valor)) {
                        $errorEncontrado = true;
                        $insertarFila = false;
                        $mensajeError = "Se encontraron errores en la columna $nombreColumna: El valor debe ser un número decimal.";
                        break 2;
                    }
                }

                // Agrega más validaciones según tus necesidades
                ${$nombreColumna} = $valor; // Crear variable dinámica
            }

            // Si se encontró un error, no se realizará ninguna inserción para la fila actual
            if (!$insertarFila) {
                break;
            }

            $sql = "INSERT INTO Data_Plantilla (";
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
    }
} else {
    echo "No se encontraron archivos en la tabla Plantilla_Dep.";
}

$conexion->close();