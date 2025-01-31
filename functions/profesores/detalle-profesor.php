<link rel="stylesheet" href="./CSS/detalle-profesor.css">

<script>
// Función para actualizar los días activos
function actualizarDiasActivos(dias, crnId, modulo, esvirtual) {
    requestAnimationFrame(() => {
        const contenedor = document.getElementById(`weekdays-${crnId}-${modulo}`);
        if (!contenedor) return;

        const diasSemana = contenedor.querySelectorAll('.day');
        
        // Limpiar todas las clases primero
        diasSemana.forEach(dia => {
            dia.classList.remove('active');
            dia.classList.remove('virtual');
        });

        // Solo agregar clases si hay días especificados
        if (dias && dias.trim() !== '' && dias !== 'Sin Datos') {
            diasSemana.forEach(dia => {
                const letraDia = dia.textContent;
                if (dias.includes(letraDia)) {
                    dia.classList.add('active');
                    if (esvirtual === 'true') {
                        dia.classList.add('virtual');
                    }
                }
            });
        }
    });
}
</script>

<?php
include './../../config/db.php';
include './funciones-horas.php';  // Añade esta línea

if(isset($_POST['codigo_profesor'])) {
    $codigo_profesor = (int)$_POST['codigo_profesor'];
    $departamento_id = (int)$_POST['departamento_id'];

    $sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM departamentos WHERE Departamento_ID = $departamento_id";
    $result_departamento = mysqli_query($conexion, $sql_departamento);
    $row_departamento = mysqli_fetch_assoc($result_departamento);
    $nombre_departamento = $row_departamento['Nombre_Departamento'];
    $departamento_nombre = $row_departamento['Departamentos'];

   // Obtener información personal del profesor
   $sql_profesor = "SELECT DISTINCT 
                        Codigo, 
                        Nombre_completo, 
                        Correo, 
                        Categoria_actual, 
                        Departamento
                    FROM coord_per_prof 
                    WHERE Codigo = ?";
                                       
    $stmt_profesor = mysqli_prepare($conexion, $sql_profesor);
    mysqli_stmt_bind_param($stmt_profesor, "i", $codigo_profesor);
    mysqli_stmt_execute($stmt_profesor);
    $result_profesor = mysqli_stmt_get_result($stmt_profesor);
    $datos_profesor = mysqli_fetch_assoc($result_profesor);

   // Obtener información personal del profesor
   $sql_profesor = "SELECT DISTINCT 
                        Codigo, 
                        Nombre_completo, 
                        Correo, 
                        Categoria_actual, 
                        Departamento
                    FROM coord_per_prof 
                    WHERE Codigo = ?";
                                       
    $stmt_profesor = mysqli_prepare($conexion, $sql_profesor);
    mysqli_stmt_bind_param($stmt_profesor, "i", $codigo_profesor);
    mysqli_stmt_execute($stmt_profesor);
    $result_profesor = mysqli_stmt_get_result($stmt_profesor);
    $datos_profesor = mysqli_fetch_assoc($result_profesor);

    // Después de $datos_profesor = mysqli_fetch_assoc($result_profesor);
    list($suma_horas, $suma_horas_definitivas, $suma_horas_temporales, 
    $horas_frente_grupo, $horas_definitivasDB) = 
    getSumaHorasPorProfesor($codigo_profesor, $conexion);

    // Obtener los valores
    if ($datos_profesor) {
        list($suma_cargo_plaza, $suma_horas_definitivas, $suma_horas_temporales, 
            $horas_frente_grupo, $horas_definitivasDB) = 
            getSumaHorasPorProfesor($codigo_profesor, $conexion);
    }

    // Funciones auxiliares para determinar la clase CSS
    function getHorasClass($actual, $esperado) {
        if ($esperado == 0 && $actual == 0) return 'horas-cero';
        if ($actual < $esperado) return 'horas-faltantes';
        if ($actual == $esperado) return 'horas-correctas';
        return 'horas-excedidas';
    }

    function limpiarNombreDepartamento($departamento) {
        // Eliminar el prefijo "Data_"
        $nombre = preg_replace('/^data_/', '', $departamento);
        // Reemplazar guiones bajos con espacios
        $nombre = str_replace('_', ' ', $nombre);
        // Convertir a mayúsculas usando mb_strtoupper para manejar correctamente caracteres especiales
        return mb_strtoupper($nombre, 'UTF-8');
    }

    // Calcular horas temporales
    $horas_temporales = $suma_horas - $suma_horas_definitivas;

    // Función para obtener todas las tablas de departamentos
    function obtenerTablasDepartamentos($conexion) {
        $sql = "SELECT Nombre_Departamento FROM departamentos";
        $result = mysqli_query($conexion, $sql);
        $tablas = [];
        while($row = mysqli_fetch_assoc($result)) {
            $tablas[] = "data_" . $row['Nombre_Departamento'];
        }
        return $tablas;
    }

    function obtenerCursosUnicos($conexion, $codigo_profesor) {
        // Obtener todas las tablas de departamentos
        $sql_departamentos = "SELECT Nombre_Departamento FROM departamentos";
        $result_departamentos = mysqli_query($conexion, $sql_departamentos);
        
        // Array para almacenar cursos únicos
        $cursos_unicos = [];
        
        // Iteración a través de cada tabla de departamentos
        while ($row_departamento = mysqli_fetch_assoc($result_departamentos)) {
            $tabla_departamento = "data_" . $row_departamento['Nombre_Departamento'];
            
            // Consulta SQL para obtener cursos únicos con manejo especial para duplicados virtuales/presenciales
            $sql_cursos = "
                SELECT 
                    CRN, 
                    MATERIA, 
                    MODALIDAD, 
                    DIA_PRESENCIAL, 
                    DIA_VIRTUAL, 
                    HORA_INICIAL,
                    HORA_FINAL, 
                    L,
                    M,
                    I,
                    J,
                    V,
                    S,
                    AULA,
                    MODULO,
                    '$tabla_departamento' AS departamento_origen
                FROM $tabla_departamento
                WHERE CODIGO_PROFESOR = ?
                AND (
                    CRN NOT IN (
                        SELECT CRN 
                        FROM $tabla_departamento 
                        WHERE CODIGO_PROFESOR = ? 
                        GROUP BY CRN 
                        HAVING COUNT(DISTINCT MODALIDAD) > 1
                    )
                    OR (
                        MODALIDAD = 'VIRTUAL' 
                        AND CRN IN (
                            SELECT CRN 
                            FROM $tabla_departamento 
                            WHERE CODIGO_PROFESOR = ? 
                            AND MODALIDAD = 'PRESENCIAL ENRIQUECIDA'
                        )
                    )
                )
                GROUP BY CRN, MATERIA, MODALIDAD, DIA_PRESENCIAL, DIA_VIRTUAL, HORA_INICIAL, HORA_FINAL, L, M, I, J, V, S, AULA, MODULO
            ";
            
            // Preparar y ejecutar la instrucción
            $stmt = mysqli_prepare($conexion, $sql_cursos);
            mysqli_stmt_bind_param($stmt, "iii", $codigo_profesor, $codigo_profesor, $codigo_profesor);
            mysqli_stmt_execute($stmt);
            $result_cursos = mysqli_stmt_get_result($stmt);
            
            // Colecciona cursos únicos
            while ($curso = mysqli_fetch_assoc($result_cursos)) {
                // Utilice CRN como clave única para evitar duplicados
                $cursos_unicos[$curso['CRN']] = $curso;
            }
            }
        // Devolver la matriz de cursos únicos
        
        return array_values($cursos_unicos);
    }

    function convertirDiasAbreviatura($dias_completos, $curso, $modalidad = null, $tipo = null) {
        $dias_abreviados = [];
        $columnas_dias = ['L', 'M', 'I', 'J', 'V', 'S'];
    
        // Lógica para manejar específicamente modalidades virtuales o presenciales enriquecidas
        if (in_array($modalidad, ['PRESENCIAL ENRIQUECIDA', 'VIRTUAL'])) {
            foreach ($columnas_dias as $dia) {
                // Verificar si el día tiene valor '1' y NO es igual al encabezado de la columna
                if (isset($curso[$dia]) && 
                    $curso[$dia] == '1' && 
                    strtolower($curso[$dia]) !== strtolower($dia)) {
                    $dias_abreviados[] = [
                        'abreviatura' => $dia,
                        'tipo' => strtolower(str_replace(' ', '_', $modalidad))
                    ];
                }
            }
    
            // Si se encontraron días, devolver esos días
            if (!empty($dias_abreviados)) {
                return $dias_abreviados;
            }
        }
    
        // Lógica original para días completos
        if ($dias_completos !== null && trim($dias_completos) !== '') {
            $dias_map = [
                'lunes' => 'L',
                'martes' => 'M',
                'miercoles' => 'I',
                'jueves' => 'J',
                'viernes' => 'V',
                'sabado' => 'S'
            ];
        
            // Convertir a minúsculas y dividir por coma
            $dias_array = array_map('trim', explode(',', mb_strtolower($dias_completos, 'UTF-8')));
            
            // Convertir cada día a su abreviatura
            $dias_abreviados = array_map(function($dia) use ($dias_map, $tipo) {
                return [
                    'abreviatura' => isset($dias_map[$dia]) ? $dias_map[$dia] : $dia,
                    'tipo' => $tipo
                ];
            }, $dias_array);
        
            return $dias_abreviados;
        }
    
        return [];
    }
    
    // Obtener información del departamento y del profesor
    $tablas_departamentos = obtenerTablasDepartamentos($conexion);
    $cursos_profesor = obtenerCursosUnicos($conexion, $codigo_profesor);

    if(!empty($cursos_profesor) && $datos_profesor) {
    ?>
            <div class="container-profesor">
                <div class="header-profesor">
                    <div class="profesor-avatar-modal">
                        <span class="avatar-initials">
                            <?php 
                                $nombres = explode(' ', $datos_profesor['Nombre_completo']);
                                echo substr($nombres[0], 0, 1) . (isset($nombres[1]) ? substr($nombres[1], 0, 1) : '');
                            ?>
                        </span>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($datos_profesor['Nombre_completo']); ?></h2>
                        <p><?php echo htmlspecialchars($datos_profesor['Correo']); ?></p>
                        <div class="profile-details">
                            <table class="table-profile">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div><span class="profile-span">Código:</span><?php echo htmlspecialchars($datos_profesor['Codigo']); ?></div>
                                        </td>
                                        <td>
                                            <div><span class="profile-span">Categoría:</span><?php echo htmlspecialchars($datos_profesor['Categoria_actual']); ?></div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="profile-span">Departamento:</span>
                                                <span class="data-value3"><?php echo strtoupper(htmlspecialchars($datos_profesor['Departamento'])); ?></span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>
                                            <span class="profile-span">Horas frente a grupo:</span>
                                            <?php
                                            if ($horas_frente_grupo == 0 && $suma_cargo_plaza == 0) {
                                                $clase = 'horas-cero';
                                            } elseif ($suma_cargo_plaza < $horas_frente_grupo) {
                                                $clase = 'horas-faltantes';
                                            } elseif ($suma_cargo_plaza == $horas_frente_grupo) {
                                                $clase = 'horas-correctas';
                                            } else {
                                                $clase = 'horas-excedidas';
                                            }
                                            ?>
                                            <span class="<?php echo $clase; ?>">
                                                <?php echo $suma_cargo_plaza; ?>/<?php echo $horas_frente_grupo; ?>
                                            </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="profile-span">Horas definitivas:</span>
                                                <?php
                                                if ($horas_definitivasDB == 0 && $suma_horas_definitivas == 0) {
                                                    $clase = 'horas-cero';
                                                } elseif ($suma_horas_definitivas < $horas_definitivasDB) {
                                                    $clase = 'horas-faltantes';
                                                } elseif ($suma_horas_definitivas == $horas_definitivasDB) {
                                                    $clase = 'horas-correctas';
                                                } else {
                                                    $clase = 'horas-excedidas';
                                                }
                                                ?>
                                                <span class="<?php echo $clase; ?>">
                                                    <?php echo $suma_horas_definitivas; ?>/<?php echo $horas_definitivasDB; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                        <div>
                                            <span class="profile-span">Horas temporales:</span>
                                            <span class="horas-temporales">
                                                <?php echo $suma_horas_temporales; ?>
                                            </span>
                                        </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <span class="close" onclick="cerrarModalDetalle()">&times;</span>
                </div>

                <div class="search-section">
                    <div class="search-box">
                        <input type="text" placeholder="Buscar..." id="search-input">
                    </div>
                    <!--  
                    <select class="department-select">
                        <option>Departamento</option>
                    </select> 
                    -->
                </div>

                <?php
                    // Cursos agrupados por nombre de la materia
                    $cursos_agrupados = [];
                    foreach ($cursos_profesor as $curso) {
                        $materia_key = $curso['MATERIA'];
                        
                        // Si esta materia no existe en la matriz agrupada, cree una nueva entrada
                        if (!isset($cursos_agrupados[$materia_key])) {
                            $cursos_agrupados[$materia_key] = [];
                        }
                        
                        // Agregar el curso actual al grupo
                        $cursos_agrupados[$materia_key][] = $curso;
                    }
                ?>

                <div class="navigation">
                    <button class="nav-arrow prev-arrow" disabled><</button>
                    <div class="nav-items-container">
                        <a href="#todas" class="nav-item active" data-section="todas">Todas las materias</a>
                        <?php
                            foreach ($cursos_agrupados as $materia => $cursos_grupo) {
                                // Utilica el primer curso del grupo para generar el elemento de navegación
                                $curso_representativo = $cursos_grupo[0];
                                
                                // Genera un identificador único para el grupo
                                $grupo_id = 'grupo_' . md5($materia);
                                
                                echo '<a href="#' . htmlspecialchars($grupo_id) . '" class="nav-item" data-section="' . htmlspecialchars($grupo_id) . '">' . 
                                    htmlspecialchars($materia) . 
                                    '</a>';
                            }
                        ?>
                    </div>
                    <button class="nav-arrow next-arrow">></button>
                </div>
                    
                <div class="navigation-space"></div>

                <!-- Contenedor para todas las secciones -->
                <div class="sections-container"> 
                    <div class="curso-seccion active" id="todas">
                        <table class="table-profesor">
                            <thead>
                                <tr>
                                    <th class="col-nrc th-L">CRN</th>
                                    <th class="col-materia">Materia</th>
                                    <th class="col-departamento">Departamento</th>
                                    <th class="col-hora">Hora</th>
                                    <th class="col-dias">Día(s)</th>
                                    <th class="col-aula">Aula</th>
                                    <th class="col-modalidad th-R">Edificio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($cursos_profesor as $curso) {
                                    // Convertir días presenciales y virtuales, manejando posibles nulos
                                    $dias_presenciales = convertirDiasAbreviatura(
                                        $curso['DIA_PRESENCIAL'], 
                                        $curso,  // Pasamos el curso completo para revisar columnas de días
                                        $curso['MODALIDAD'], 
                                        'presencial'
                                    );
                                    $dias_virtuales = convertirDiasAbreviatura(
                                        $curso['DIA_VIRTUAL'], 
                                        $curso, 
                                        $curso['MODALIDAD'], 
                                        'virtual'
                                    );

                                    // Combinar y eliminar duplicados
                                    $dias_curso = [];
                                    $dias_combinados = array_merge($dias_presenciales, $dias_virtuales);

                                    // Crear un mapa de días con su tipo
                                    foreach ($dias_combinados as $dia) {
                                        $dias_curso[$dia['abreviatura']] = $dia['tipo'] ?? null;
                                    }

                                    $hora_inicio = $curso['HORA_INICIAL'] ?? '0000';
                                    $hora_fin = $curso['HORA_FINAL'] ?? '0000';
                                    $horarioFormateado = sprintf('%s - %s', 
                                        substr($hora_inicio, 0, 2) . ':' . substr($hora_inicio, 2, 2), 
                                        substr($hora_fin, 0, 2) . ':' . substr($hora_fin, 2, 2)
                                    );

                                    $fila_todas = '
                                    <tr>
                                        <td class="col-nrc">' . htmlspecialchars($curso['CRN']) . '</td>
                                        <td class="col-materia">' . htmlspecialchars($curso['MATERIA']) . '</td>
                                        <td class="col-departamento">' . htmlspecialchars(limpiarNombreDepartamento($curso['departamento_origen'])) . '</td>
                                        <td class="col-hora">' .  htmlspecialchars($horarioFormateado) . '</td>
                                        <td class="col-dias">
                                            <div class="weekdays" id="weekdays-' . htmlspecialchars($curso['CRN']) . '-' . htmlspecialchars($curso['MODULO'] ?? 'NA') . '">';
                                        
                                            if($curso['MODALIDAD'] === "PRESENCIAL ENRIQUECIDA" || $curso['MODALIDAD'] === null){
                                                $dias = '';
                                                $hayDias = false;
                                                
                                                // Verificar si hay algún día asignado
                                                if($curso['L'] == 'L') { $dias .= 'L'; $hayDias = true; }
                                                if($curso['M'] == 'M') { $dias .= 'M'; $hayDias = true; }
                                                if($curso['I'] == 'I') { $dias .= 'I'; $hayDias = true; }
                                                if($curso['J'] == 'J') { $dias .= 'J'; $hayDias = true; }
                                                if($curso['V'] == 'V') { $dias .= 'V'; $hayDias = true; }
                                                if($curso['S'] == 'S') { $dias .= 'S'; $hayDias = true; }
                                            
                                                $fila_todas .= '
                                                    <div class="day">L</div>
                                                    <div class="day">M</div>
                                                    <div class="day">I</div>
                                                    <div class="day">J</div>
                                                    <div class="day">V</div>
                                                    <div class="day">S</div>';
                                            
                                                if($hayDias) {
                                                    $fila_todas .= '
                                                    <script>
                                                    actualizarDiasActivos(
                                                        "' . htmlspecialchars($dias) . '", 
                                                        "' . htmlspecialchars($curso["CRN"]) . '", 
                                                        "' . htmlspecialchars($curso["MODULO"] ?? "NA") . '", 
                                                        "' . ($curso["MODULO"] === "CVIRTU" ? "true" : "false") . '"
                                                    );
                                                    </script>';
                                                }

                                            } elseif ($curso['MODALIDAD'] === "VIRTUAL") {
                                                $dias = '';
                                                if($curso['L'] == 'L') $dias .= 'L';
                                                if($curso['M'] == 'M') $dias .= 'M';
                                                if($curso['I'] == 'I') $dias .= 'I';
                                                if($curso['J'] == 'J') $dias .= 'J';
                                                if($curso['V'] == 'V') $dias .= 'V';
                                                if($curso['S'] == 'S') $dias .= 'S';
                                                if($dias == '') $dias .= 'Sin Datos';

                                                $fila_todas .= '
                                                <div class="day">L</div>
                                                <div class="day">M</div>
                                                <div class="day">I</div>
                                                <div class="day">J</div>
                                                <div class="day">V</div>
                                                <div class="day">S</div>

                                                <script>
                                                actualizarDiasActivos(
                                                    "' . htmlspecialchars($dias) . '", 
                                                    "' . htmlspecialchars($curso["CRN"]) . '", 
                                                    "' . htmlspecialchars($curso["MODULO"] ?? "NA") . '", 
                                                    "' . ($curso["MODULO"] === "CVIRTU" ? "true" : "false") . '"
                                                );
                                                </script>
                                                ';
                                            } else {
                                                $dias_semana = ['L', 'M', 'I', 'J', 'V', 'S'];
            
                                                foreach ($dias_semana as $dia) {
                                                    $tipo_dia = $dias_curso[$dia] ?? null;
                                                    $class = '';
                                                    
                                                    if ($tipo_dia === 'presencial') {
                                                        $class = 'active presencial';
                                                    } elseif ($tipo_dia === 'virtual') {
                                                        $class = 'active virtual';
                                                    }
                                                    
                                                    $fila_todas .= '
                                                        <div class="day ' . $class . '">' . $dia . '</div>';
                                                }
                                            }
                                            
                                    
                                    $fila_todas .= '
                                            </div>
                                        </td>

                                        <td class="col-aula">' . htmlspecialchars($curso['AULA'] ?? 'No hay datos') . '</td>
                                        <td class="col-modalidad">' . htmlspecialchars($curso['MODULO'] . ' (' . $curso['MODALIDAD'] . ')' ?? 'No hay datos') . '</td>
                                    </tr>';
                                    
                                    echo $fila_todas;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Aquí se añaden secciones individuales del curso de forma dinámica -->
                    <?php
                        // Genera secciones de curso agrupadas
                        foreach ($cursos_agrupados as $materia => $cursos_grupo) {
                            $grupo_id = 'grupo_' . md5($materia);
                            ?>
                            <div class="curso-seccion" id="<?php echo htmlspecialchars($grupo_id); ?>">
                                <table class="table-profesor">
                                    <thead>
                                        <tr>
                                            <th class="col-nrc th-L">CRN</th>
                                            <th class="col-materia">Materia</th>
                                            <th class="col-departamento">Departamento</th>
                                            <th class="col-hora">Hora</th>
                                            <th class="col-dias">Día(s)</th>
                                            <th class="col-aula">Aula</th>
                                            <th class="col-modalidad th-R">Edificio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Renderiza cada curso en el grupo
                                        foreach ($cursos_grupo as $curso) {
                                            // Convertir días presenciales y virtuales
                                            $dias_presenciales = convertirDiasAbreviatura(
                                                $curso['DIA_PRESENCIAL'], 
                                                $curso,  // Pasamos el curso completo para revisar columnas de días
                                                $curso['MODALIDAD'], 
                                                'presencial'
                                            );
                                            $dias_virtuales = convertirDiasAbreviatura(
                                                $curso['DIA_VIRTUAL'], 
                                                $curso, 
                                                $curso['MODALIDAD'], 
                                                'virtual'
                                            );

                                            // Combinar y eliminar duplicados
                                            $dias_curso = [];
                                            $dias_combinados = array_merge($dias_presenciales, $dias_virtuales);

                                            // Crear un mapa de días con su tipo
                                            foreach ($dias_combinados as $dia) {
                                                $dias_curso[$dia['abreviatura']] = $dia['tipo'] ?? null;
                                            }

                                            $hora_inicio = $curso['HORA_INICIAL'] ?? '0000';
                                            $hora_fin = $curso['HORA_FINAL'] ?? '0000';
                                            $horarioFormateado = sprintf('%s - %s', 
                                                substr($hora_inicio, 0, 2) . ':' . substr($hora_inicio, 2, 2), 
                                                substr($hora_fin, 0, 2) . ':' . substr($hora_fin, 2, 2)
                                            );
                                            
                                            $fila_curso = '
                                            <tr>
                                                <td class="col-nrc">' . htmlspecialchars($curso['CRN']) . '</td>
                                                <td class="col-materia">' . htmlspecialchars($curso['MATERIA']) . '</td>
                                                <td class="col-departamento">' . htmlspecialchars(limpiarNombreDepartamento($curso['departamento_origen'])) . '</td>
                                                <td class="col-hora">' .  htmlspecialchars($horarioFormateado) . '</td>
                                                <td class="col-dias">
                                                    <div class="weekdays" id="weekdays-' . htmlspecialchars($curso['CRN']) . '-' . htmlspecialchars($curso['MODULO'] ?? 'NA') . '">';
                                            
                                                    if($curso['MODALIDAD'] === "PRESENCIAL ENRIQUECIDA" || $curso['MODALIDAD'] === null){
                                                        if($curso['L'] == 'L'){
                                                            $fila_curso .= '
                                                                <div class="day active">L</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">L</div>
                                                            ';
                                                        }
                                                        if($curso['M'] == 'M'){
                                                            $fila_curso .= '
                                                                <div class="day active">M</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">M</div>
                                                            ';
                                                        }
                                                        if($curso['I'] == 'I'){
                                                            $fila_curso .= '
                                                                <div class="day active">I</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">I</div>
                                                            ';
                                                        }
                                                        if($curso['J'] == 'J'){
                                                            $fila_curso .= '
                                                                <div class="day active">J</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">J</div>
                                                            ';
                                                        }
                                                        if($curso['V'] == 'V'){
                                                            $fila_curso .= '
                                                                <div class="day active">V</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">V</div>
                                                            ';
                                                        }
                                                        if($curso['S'] == 'S'){
                                                            $fila_curso .= '
                                                                <div class="day active">S</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">S</div>
                                                            ';
                                                        }
                                                        /*
                                                        $dias = '';
                                                        if($curso['L'] == 'L') $dias .= 'L';
                                                        if($curso['M'] == 'M') $dias .= 'M';
                                                        if($curso['I'] == 'I') $dias .= 'I';
                                                        if($curso['J'] == 'J') $dias .= 'J';
                                                        if($curso['V'] == 'V') $dias .= 'V';
                                                        if($curso['S'] == 'S') $dias .= 'S';
                                                        if($dias == '') $dias .= 'Sin Datos';

                                                        
                                                        $fila_curso .= '
                                                        <div class="day">L</div>
                                                        <div class="day">M</div>
                                                        <div class="day">I</div>
                                                        <div class="day">J</div>
                                                        <div class="day">V</div>
                                                        <div class="day">S</div>
        
                                                        <script>
                                                            actualizarDiasActivos(
                                                                "' . htmlspecialchars($dias) . '", 
                                                                "' . htmlspecialchars($curso["CRN"]) . '", 
                                                                "' . htmlspecialchars($curso["MODULO"] ?? "NA") . '", 
                                                                "' . ($curso["MODULO"] === "CVIRTU" ? "true" : "false") . '"
                                                            );
                                                        </script>
                                                        ';
                                                        */
        
                                                    } elseif ($curso['MODALIDAD'] === "VIRTUAL") {
                                                        if($curso['L'] == 'L'){
                                                            $fila_curso .= '
                                                                <div class="day active virtual">L</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">L</div>
                                                            ';
                                                        }
                                                        if($curso['M'] == 'M'){
                                                            $fila_curso .= '
                                                                <div class="day active virtual">M</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">M</div>
                                                            ';
                                                        }
                                                        if($curso['I'] == 'I'){
                                                            $fila_curso .= '
                                                                <div class="day active virtual">I</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">I</div>
                                                            ';
                                                        }
                                                        if($curso['J'] == 'J'){
                                                            $fila_curso .= '
                                                                <div class="day active virtual">J</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">J</div>
                                                            ';
                                                        }
                                                        if($curso['V'] == 'V'){
                                                            $fila_curso .= '
                                                                <div class="day active virtual">V</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">V</div>
                                                            ';
                                                        }
                                                        if($curso['S'] == 'S'){
                                                            $fila_curso .= '
                                                                <div class="day active virtual">S</div>
                                                            ';
                                                        } else {
                                                            $fila_curso .= '
                                                                <div class="day">S</div>
                                                            ';
                                                        }

                                                        /*
                                                        $dias = '';
                                                        if($curso['L'] == 'L') $dias .= 'L';
                                                        if($curso['M'] == 'M') $dias .= 'M';
                                                        if($curso['I'] == 'I') $dias .= 'I';
                                                        if($curso['J'] == 'J') $dias .= 'J';
                                                        if($curso['V'] == 'V') $dias .= 'V';
                                                        if($curso['S'] == 'S') $dias .= 'S';
                                                        if($dias == '') $dias .= 'Sin Datos';
        
                                                        $fila_curso .= '
                                                        <div class="day">L</div>
                                                        <div class="day">M</div>
                                                        <div class="day">I</div>
                                                        <div class="day">J</div>
                                                        <div class="day">V</div>
                                                        <div class="day">S</div>
        
                                                        <script>
                                                            actualizarDiasActivos(
                                                                "' . htmlspecialchars($dias) . '", 
                                                                "' . htmlspecialchars($curso["CRN"]) . '", 
                                                                "' . htmlspecialchars($curso["MODULO"] ?? "NA") . '", 
                                                                "' . ($curso["MODULO"] === "CVIRTU" ? "true" : "false") . '"
                                                            );
                                                        </script>
                                                        ';
                                                        */
                                                    } else {
                                                        $dias_semana = ['L', 'M', 'I', 'J', 'V', 'S'];
                    
                                                        foreach ($dias_semana as $dia) {
                                                            $tipo_dia = $dias_curso[$dia] ?? null;
                                                            $class = '';
                                                            
                                                            if ($tipo_dia === 'presencial') {
                                                                $class = 'active presencial';
                                                            } elseif ($tipo_dia === 'virtual') {
                                                                $class = 'active virtual';
                                                            }
                                                            
                                                            $fila_curso .= '
                                                                <div class="day ' . $class . '">' . $dia . '</div>';
                                                        }
                                                    }
                                    
                                            $fila_curso .= '
                                                    </div>
                                                </td>
                                                <td class="col-aula">' . htmlspecialchars($curso['AULA'] ?? 'No hay datos') . '</td>
                                                <td class="col-modalidad">' . htmlspecialchars($curso['MODULO'] . ' (' . $curso['MODALIDAD'] . ')' ?? 'No hay datos') . '</td>
                                            </tr>';
                                            
                                            echo $fila_curso;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
        <?php
    } else {
        ?>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Información del Profesor</h3>
                <span class="close1" onclick="cerrarModalDetalle()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">No se encontró información para este profesor.</div>
            </div>
        </div>
    <?php    
    }
}
?>
<script src="./JS/profesores/materias.js"></script>
<script src="./JS/profesores/profesores-materias.js"></script>