<?php
include './../../config/db.php';
if (!isset($_GET['departamento'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetro departamento no proporcionado']);
    exit;
}

$departamento = $_GET['departamento'];

try {
    if ($departamento === 'todos') {
        $query = "SELECT 
            Codigo,
            Nombre_completo,
            Tipo_plaza,
            Horas_frente_grupo,
            Carga_horaria,
            Horas_definitivas
        FROM Coord_Per_Prof
        ORDER BY Nombre_completo";

        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            throw new Exception(mysqli_error($conn));
        }
    } else {
        $query = "SELECT 
            Codigo,
            Nombre_completo,
            Tipo_plaza,
            Horas_frente_grupo,
            Carga_horaria,
            Horas_definitivas
        FROM Coord_Per_Prof
        WHERE LOWER(Departamento) = LOWER(?)
        ORDER BY Nombre_completo";
        
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "s", $departamento);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'codigo' => $row['Codigo'] ?? '',
            'nombre_completo' => $row['Nombre_completo'] ?? '',
            'tipo_plaza' => $row['Tipo_plaza'] ?? '',
            'horas_frente_grupo' => $row['Horas_frente_grupo'] ?? '0',
            'carga_horaria' => $row['Carga_horaria'] ?? '',
            'horas_definitivas' => $row['Horas_definitivas'] ?? '0'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>