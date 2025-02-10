<?php
// guardar_solicitud_baja.php
session_start();
include './../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$conexion) {
            throw new Exception("Error de conexión a la base de datos");
        }

        $nombres = mysqli_real_escape_string($conexion, $_POST['nombres']);
        $apellido_paterno = mysqli_real_escape_string($conexion, $_POST['apellido_paterno']);
        $apellido_materno = mysqli_real_escape_string($conexion, $_POST['apellido_materno']);
        $codigo = mysqli_real_escape_string($conexion, $_POST['codigo']);
        $profesion = mysqli_real_escape_string($conexion, $_POST['profesion']);
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
        $clasificacion = mysqli_real_escape_string($conexion, $_POST['clasificacion']);
        $motivo = mysqli_real_escape_string($conexion, $_POST['motivo']);
        $crn = mysqli_real_escape_string($conexion, $_POST['crn']);
        $fecha_efectos = mysqli_real_escape_string($conexion, $_POST['fecha_efectos']);
        $oficio_num = mysqli_real_escape_string($conexion, $_POST['oficio_num']);
        $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);

        $sql = "INSERT INTO solicitudes_baja (
            NOMBRES_PROF_B, APELLIDO_P_PROF_B, APELLIDO_M_PROF_B,
            PROFESSION_PROFESOR_B, CODIGO_PROF_B, DESCRIPCION_PUESTO_B,
            CLASIFICACION_BAJA_B, MOTIVO_B, CRN_B,
            SIN_EFFECTOS_DESDE_B, OFICIO_NUM_BAJA, FECHA_SOLICITUD_B,
            ESTADO_B
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente')";

        $stmt = $conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta");
        }

        $stmt->bind_param("ssssssssssss",
            $profesion, $apellido_paterno, $apellido_materno,
            $nombres, $codigo, $descripcion,
            $clasificacion, $motivo, $crn,
            $fecha_efectos, $oficio_num, $fecha
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Solicitud guardada exitosamente']);
        } else {
            throw new Exception("Error al ejecutar la consulta");
        }

        $stmt->close();

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    $conexion->close();
}