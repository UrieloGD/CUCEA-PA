<?php
include '../config/db.php';
include './email_functions.php';
session_start();
date_default_timezone_set('America/Mexico_City');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departamento_id = $_POST['Departamento_ID'];
    error_log("Departamento ID: $departamento_id");

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo_dep = $_FILES['file']['name'];
        $fecha_subida_dep = date('d/m/Y H:i');
        $archivo_temporal = $_FILES['file']['tmp_name'];

        // Leer el contenido del archivo
        $contenido_archivo_dep = file_get_contents($archivo_temporal);
        $contenido_archivo_dep = mysqli_real_escape_string($conexion, $contenido_archivo_dep);

        error_log("Archivo temporal: $archivo_temporal");

        // Insertar los datos en la base de datos
        $sql = "INSERT INTO Plantilla_SA (Departamento_ID, Nombre_Archivo_Dep, Fecha_Subida_Dep, Contenido_Archivo_Dep)
                VALUES ('$departamento_id', '$nombre_archivo_dep', '$fecha_subida_dep', '$contenido_archivo_dep')";

        if (mysqli_query($conexion, $sql)) {
            error_log("Inserción en la base de datos exitosa");

            // Obtener el correo del jefe de departamento
            $sql_jefe = "SELECT u.Correo, d.Nombre_Departamento 
                         FROM Usuarios u 
                         JOIN Usuarios_Departamentos ud ON u.Codigo = ud.Usuario_ID 
                         JOIN Departamentos d ON ud.Departamento_ID = d.Departamento_ID 
                         WHERE d.Departamento_ID = ? AND u.Rol_ID = 1";
            $stmt = mysqli_prepare($conexion, $sql_jefe);
            mysqli_stmt_bind_param($stmt, "i", $departamento_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $jefe = mysqli_fetch_assoc($result);

            if ($jefe) {
                $asunto = "Nueva plantilla subida por Secretaría Administrativa";
                $cuerpo = "
                <html>
                <body>
                    <h2>Nueva plantilla subida</h2>
                    <p>Se ha subido una nueva plantilla para el departamento de {$jefe['Nombre_Departamento']}.</p>
                    <p>Nombre del archivo: {$nombre_archivo_dep}</p>
                    <p>Fecha de subida: {$fecha_subida_dep}</p>
                    <p>Por favor, ingrese al sistema para más detalles.</p>
                </body>
                </html>
                ";

                try {
                    if (enviarCorreo($jefe['Correo'], $asunto, $cuerpo)) {
                        error_log("Correo enviado exitosamente al jefe del departamento {$jefe['Nombre_Departamento']}");
                    } else {
                        error_log("Error al enviar correo al jefe del departamento {$jefe['Nombre_Departamento']}");
                    }
                } catch (Exception $e) {
                    error_log("Excepción al enviar correo: " . $e->getMessage());
                }
            } else {
                error_log("No se encontró jefe de departamento para el Departamento_ID: $departamento_id");
            }

            echo 'success';
            exit();
        } else {
            error_log("Error al insertar en la base de datos: " . mysqli_error($conexion));
            echo 'Error al añadir registro: ' . mysqli_error($conexion);
            exit();
        }
    } else {
        error_log("Error en la subida de archivo: " . $_FILES['file']['error']);
        echo 'Error: No se subió ningún archivo o hubo un error en la subida.';
        exit();
    }
} else {
    error_log("Método de solicitud no permitido");
    echo 'Error: Método de solicitud no permitido.';
    exit();
}

mysqli_close($conexion);
