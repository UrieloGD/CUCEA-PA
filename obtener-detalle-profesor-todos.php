<?php
<?php
include '../config/db.php';

$codigo = $_POST['codigo'];

$sql = "SELECT * FROM Coord_Per_Prof WHERE Codigo = ?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "s", $codigo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$profesor = mysqli_fetch_assoc($result);

// Devolver los datos como JSON
echo json_encode($profesor);

mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>