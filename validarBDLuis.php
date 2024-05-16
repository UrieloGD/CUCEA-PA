<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta SQL para seleccionar todos los registros
$sql = "SELECT * FROM Data_Plantilla";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Recorrer los resultados
    while ($row = $result->fetch_assoc()) {
        $idPlantilla = $row["ID_Plantilla"];
        $ciclo = $row["CICLO"];
        $nrc = $row["NRC"];
        $l = $row["L"];
        $m = $row["M"];
        $i = $row["I"];
        $j = $row["J"];
        $v = $row["V"];
        $s = $row["S"];
        $d = $row["D"];
        $horaIni = $row["HORA INI"];
        $horaFin = $row["HORA FIN"];
        $edif = $row["EDIF"];
        $aula = $row["AULA"];

        // Validaciones
        $errores = array();

        // CICLO: debe contener no más de 6 caracteres
        if (strlen($ciclo) > 6) {
            $errores[] = "El valor '$ciclo' en la columna CICLO no debe tener más de 6 caracteres.";
        }

        // NRC: debe ser un valor numérico no mayor a 7 dígitos
        if (!is_numeric($nrc) || strlen($nrc) > 7) {
            $errores[] = "El valor '$nrc' en la columna NRC no es un número válido o tiene más de 7 dígitos.";
        }

        // L: Solo debe contener la letra L
        if ($l != "L" && !empty($l)) {
            $errores[] = "El valor '$l' en la columna L no es válido. Debe contener solo la letra L.";
        }

        // M: Solo debe contener la letra M
        if ($m != "M" && !empty($m)) {
            $errores[] = "El valor '$m' en la columna M no es válido. Debe contener solo la letra M.";
        }

        // I: Solo debe contener la letra I
        if ($i != "I" && !empty($i)) {
            $errores[] = "El valor '$m' en la columna I no es válido. Debe contener solo la letra I.";
        }

        // J: Solo debe contener la letra J
        if ($j != "J" && !empty($j)) {
            $errores[] = "El valor '$j' en la columna J no es válido. Debe contener solo la letra J.";
        }

        // V: Solo debe contener la letra V
        if ($v != "V" && !empty($v)) {
            $errores[] = "El valor '$v' en la columna V no es válido. Debe contener solo la letra V.";
        }

        // S: Solo debe contener la letra S
        if ($s != "S" && !empty($s)) {
            $errores[] = "El valor '$s' en la columna S no es válido. Debe contener solo la letra S.";
        }

        // D: Solo debe contener la letra D
        if ($d != "D" && !empty($d)) {
            $errores[] = "El valor '$d' en la columna D no es válido. Debe contener solo la letra D.";
        }

        // HORA INI: debe ser un valor numérico entre 3 y 4 dígitos
        if (!is_numeric($horaIni) || (strlen($horaIni) < 3 && strlen($horaIni) > 4)) {
            $errores[] = "El valor '$horaIni' en la columna HORA INI no es un número válido o no tiene entre 3 y 4 dígitos.";
        }

        // HORA FIN: debe ser un valor numérico entre 3 y 4 dígitos
        if (!is_numeric($horaFin) || (strlen($horaFin) < 3 && strlen($horaFin) > 4)) {
            $errores[] = "El valor '$horaFin' en la columna HORA FIN no es un número válido o no tiene entre 3 y 4 dígitos.";
        }

        // EDIF: debe contener no más de 6 caracteres
        if (strlen($edif) > 6) {
            $errores[] = "El valor '$edif' en la columna EDIF no debe tener más de 6 caracteres.";
        }

        // AULA: debe ser un valor numérico no mayor a 7 dígitos
        // *** NOTA ****: Revisar que cuando $edif = CVIRTU, el espacio aula esté vacío.
        if(!empty($aula)){
            if (!is_numeric($aula) || strlen($aula) > 7) {
                $errores[] = "El valor '$aula' en la columna AULA no es un número válido o tiene más de 7 dígitos.";
            }
        }
        else
        {
            $errores[] = "Advertencia: El valor en la columna AULA está vacío";
        }


        // Mostrar los errores encontrados
        if (!empty($errores)) {
            echo "Errores en el registro con ID_Plantilla: $idPlantilla<br>";
            foreach ($errores as $error) {
                echo "- $error<br>";
            }
            echo "<br>";
        }
    }
} else {
    echo "No se encontraron registros en la tabla Data_Plantilla.";
}

// Cerrar la conexión
$conn->close();

?>