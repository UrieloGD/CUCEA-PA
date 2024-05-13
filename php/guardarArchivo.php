<?php
// Incluir la conexión a la base de datos
require_once './config/db.php';

function guardarArchivo($usuario_id, $nombre_archivo) {
    global $conexion;

    // Obtener el rol del usuario
    $sql_rol = "SELECT Rol_ID FROM Usuarios WHERE Codigo = $usuario_id";
    $resultado_rol = $conexion->query($sql_rol);

    if ($resultado_rol->num_rows > 0) {
        $fila_rol = $resultado_rol->fetch_assoc();
        $rol_id = $fila_rol['Rol_ID'];

        // Verificar si el usuario es Jefe de Departamento (rol_id = 1)
        if ($rol_id == 1) {
            // Obtener el departamento al que pertenece el usuario
            $sql_departamento = "SELECT Departamento_ID FROM Usuarios_Departamentos WHERE Usuario_ID = $usuario_id LIMIT 1";
            $resultado_departamento = $conexion->query($sql_departamento);

            if ($resultado_departamento->num_rows > 0) {
                $fila_departamento = $resultado_departamento->fetch_assoc();
                $departamento_id = $fila_departamento['Departamento_ID'];

                // Guardar el archivo en la tabla Plantilla_Dep
                $ruta_archivo = 'uploads/' . $nombre_archivo;
                $tamano_archivo = $_FILES['archivo']['size'];

                $sql_insertar = "INSERT INTO Plantilla_Dep (Nombre_Archivo_Dep, Ruta_Archivo_Dep, Tamaño_Archivo_Dep, Usuario_ID, Departamento_ID) VALUES ('$nombre_archivo', '$ruta_archivo', $tamano_archivo, $usuario_id, $departamento_id)";

                if ($conexion->query($sql_insertar) === TRUE) {
                    echo "El archivo se ha subido correctamente en la tabla Plantilla_Dep.";
                } else {
                    echo "Error al subir el archivo: " . $conexion->error;
                }
            } else {
                echo "Error al obtener el departamento del usuario.";
            }
        } else {
            // Guardar el archivo en la tabla Plantilla_SA
            $ruta_archivo = 'uploads/' . $nombre_archivo;
            $tamano_archivo = $_FILES['archivo']['size'];

            $sql_insertar = "INSERT INTO Plantilla_SA (Nombre_Archivo_SA, Ruta_Archivo_SA, Tamaño_Archivo_SA, Usuario_ID) VALUES ('$nombre_archivo', '$ruta_archivo', $tamano_archivo, $usuario_id)";

            if ($conexion->query($sql_insertar) === TRUE) {
                echo "El archivo se ha subido correctamente en la tabla Plantilla_SA.";
            } else {
                echo "Error al subir el archivo: " . $conexion->error;
            }
        }
    } else {
        echo "Error al obtener el rol del usuario.";
    }

    $conexion->close();
}