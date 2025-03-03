    <?php
    session_start();

    if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 3) {
        header("Location: home.php");
        exit();
    }
    ?>

    <?php include './template/header.php' ?>
    <?php include './template/navbar.php' ?>
    <?php require_once './config/db.php'; ?>

    <title>Revisión de horas asignadas</title>
    <link rel="stylesheet" href="./CSS/horas-comparacion.css">

    <?php
    $departamentos = [
        [
            'id' => 1,
            'nombre' => 'Administración',
            'imagen' => 'administracion.png',
            'color' => '#66CAE8',
            'nombre_lineas' => ['Administración'],
            'style_imagen' => '',
            'es_arriba' => false,
            'multilinea' => false
        ],
        [
            'id' => 2,
            'nombre' => 'Ciencias Sociales y Jurídicas',
            'imagen' => 'ciencias_s.png',
            'color' => '#0C9DEB',
            'nombre_lineas' => ['Ciencias Sociales', 'y Jurídicas'],
            'style_imagen' => '',
            'es_arriba' => false,
            'multilinea' => true
        ],
        [
            'id' => 3,
            'nombre' => 'Economía',
            'imagen' => 'economia.png', 
            'color' => '#F792B4',
            'nombre_lineas' => ['Economía'],
            'style_imagen' => '',
            'es_arriba' => false,
            'multilinea' => false
        ],
        [
            'id' => 4,
            'nombre' => 'Finanzas',
            'imagen' => 'finanzas.png',
            'color' => '#9AD156',
            'nombre_lineas' => ['Finanzas'],
            'style_imagen' => '',
            'es_arriba' => false,
            'multilinea' => false
        ],
        [
            'id' => 5,
            'nombre' => 'Mercadotecnia y Negocios Internacionales',
            'imagen' => 'merc_negocios.png',
            'color' => '#51B0A3',
            'nombre_lineas' => ['Mercadotecnia y Negocios', 'Internacionales'],
            'style_imagen' => 'width: 105%;',
            'es_arriba' => true,
            'multilinea' => true
        ],
        [
            'id' => 6,
            'nombre' => 'PALE',
            'imagen' => 'pale.png',
            'color' => '#9F7FAD',
            'nombre_lineas' => ['PALE'],
            'style_imagen' => 'width: 85%;',
            'es_arriba' => true,
            'multilinea' => false
        ],
        [
            'id' => 7,
            'nombre' => 'Posgrados',
            'imagen' => 'posgrados.png',
            'color' => '#D82C8C',
            'nombre_lineas' => ['Posgrados'],
            'style_imagen' => '',
            'es_arriba' => true,
            'multilinea' => false
        ],
        [
            'id' => 8,
            'nombre' => 'Sistemas de la Información',
            'imagen' => 'sistemas.png',
            'color' => '#00B567',
            'nombre_lineas' => ['Sistemas de la Información'],
            'style_imagen' => 'width: 90%;',
            'es_arriba' => true,
            'multilinea' => false
        ],
        [
            'id' => 9,
            'nombre' => 'Auditoría',
            'imagen' => 'auditoria.png',
            'color' => '#A50F62',
            'nombre_lineas' => ['Auditoría'],
            'style_imagen' => '',
            'es_arriba' => false,
            'multilinea' => false
        ],
        [
            'id' => 10,
            'nombre' => 'Contabilidad',
            'imagen' => 'contabilidad.png',
            'color' => '#FD7C6C',
            'nombre_lineas' => ['Contabilidad'],
            'style_imagen' => '',
            'es_arriba' => false,
            'multilinea' => false
        ],
        [
            'id' => 11,
            'nombre' => 'Estudios Regionales',
            'imagen' => 'regionales.png',
            'color' => '#D72B34',
            'nombre_lineas' => ['Estudios Regionales'],
            'style_imagen' => 'width: 95%;',
            'es_arriba' => false,
            'multilinea' => false
        ],
        [
            'id' => 12,
            'nombre' => 'Impuestos',
            'imagen' => 'impuestos.png',
            'color' => '#E87C00',
            'nombre_lineas' => ['Impuestos'],
            'style_imagen' => '',
            'es_arriba' => false,
            'multilinea' => false
        ],
        [
            'id' => 13,
            'nombre' => 'Métodos Cuantitativos',
            'imagen' => 'metodos.png',
            'color' => '#F5C938',
            'nombre_lineas' => ['Métodos Cuantitativos'],
            'style_imagen' => '',
            'es_arriba' => true,
            'multilinea' => false
        ],
        [
            'id' => 14,
            'nombre' => 'Políticas Públicas',
            'imagen' => 'politicas.png',
            'color' => '#4D4024',
            'nombre_lineas' => ['Políticas Públicas'],
            'style_imagen' => '',
            'es_arriba' => true,
            'multilinea' => false
        ],
        [
            'id' => 15,
            'nombre' => 'Recursos Humanos',
            'imagen' => 'rh.png',
            'color' => '#B89358',
            'nombre_lineas' => ['Recursos Humanos'],
            'style_imagen' => '',
            'es_arriba' => true,
            'multilinea' => false
        ],
        [
            'id' => 16,
            'nombre' => 'Turismo',
            'imagen' => 'turismo.png',
            'color' => '#628EBD',
            'nombre_lineas' => ['Turismo'],
            'style_imagen' => 'width: 105%;',
            'es_arriba' => true,
            'multilinea' => false
        ]
    ];

        function generarDepartamento($depto) {
            $esArriba = $depto['es_arriba'] ? 'true' : 'false';
            
            $nombreLineas = implode('', array_map(function($linea, $index) use ($depto) {
                // Solo aplicar estilos si es multilínea
                if ($depto['multilinea']) {
                    $bottom = ($index == 0) ? '-5px' : '-3px'; // Primera línea 10px, segunda 30px
                    return "<p style='position:relative; bottom:{$bottom}; margin:0;'>$linea</p>";
                }
                return "<p>$linea</p>"; // Sin estilos si no es multilínea
            }, $depto['nombre_lineas'], array_keys($depto['nombre_lineas']))); // Pasamos el índice

        return <<<HTML
        <div class="contenedor-de-contenedores">
            <div class="departamento-contenedor-principal" 
                onclick="mostrarInformacion('contenedor-informacion-{$depto['id']}', this.querySelector('.icono-despliegue i'), $esArriba)">
                <div class="espacio-icono">
                    <img class="icono-dpto" 
                        src="./Img/Icons/iconos-horas-comparacion/departamentos/{$depto['imagen']}" 
                        alt="{$depto['nombre']}" 
                        style="{$depto['style_imagen']}">
                </div>
                <div class="titulo-dpto">
                    $nombreLineas
                </div>
                <div class="icono-despliegue">
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                </div>
            </div>
            <div class="contenedor-informacion" id="contenedor-informacion-{$depto['id']}">
                <div class="hrs-totales-dpto_container">
                    <p class="titulo-totales-dpto">Horas totales</p>
                    <div class="borde-barra-stats-hrs" style="border: 3px solid {$depto['color']};">
                    <div class="barra-stats-hrs" style="background-color: {$depto['color']};">
                        <p class="porcentaje-dpto" 
                        style="color: white; font-weight: bold; margin: 0; position: absolute; width: 100%; text-align: center; line-height: 30px;">
                        60%
                        </p>
                    </div>
                </div>
                    <p class="horas-comp-dpto">5,117 / <strong>10,234</strong></p>
                    <div class="titulo-underline" style="width:100%;"></div>
                </div>
                <div class="ultima-mod-dpto_container">
                    <p class="titulo-totales-dpto">Última modificación</p>
                    <table class="tabla-ultimas-mod-dpto">
                        <thead class="encabezado-ultimas-mod-dpto" style="background-color: {$depto['color']};">
                            <tr>
                                <td>Fecha</td>
                                <td>Hora</td>
                                <td>Responsable</td>
                            </tr>
                        </thead>
                        <tbody class="cuerpo-ultimas-mod-dpto">
                            <tr>
                                <td>23/10/24</td>
                                <td>13:00</td>
                                <td>Rafael Castanedo Escobedo</td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="desglose-button-dpto" 
                            data-departamento="{$depto['nombre']}" 
                            style="background-color: {$depto['color']};">Desglose</button>
                </div>
            </div>
        </div>
    HTML;
    }
    ?>

    <div class="cuadro-principal">
        <div class="encabezado">
            <div class="titulo-bd">
                <h3>Revisión de horas asignadas</h3>
            </div>
        </div>

        <div class="contenedor-resumen-full">
            <div class="cuadro-resumen">
                <div class="titulo-resumen">
                    <img src="./Img/Icons/iconos-horas-comparacion/cuadro-resumen/titulo_icon.png" alt="Icono resumen">
                    <p>Todos los departamentos</p>
                </div>
                <div class="titulo-underline"></div>
                
                <div class="total-general-hrs_container">
                    <p class="titulo-total-general">Total general de horas</p>
                    <div class="stats-general-hrs">
                        <div class="stats-grafica">
                            <div class="circulo-progreso">
                                <div class="circulo">
                                    <span class="porcentaje" id="porcentaje-general">50%</span>
                                </div>
                            </div>
                        </div>
                        <p id="horas-comp-general">5,117 / <strong>10,234</strong></p>
                        <button class="desglose-button" id="desglose-todos">Desglose</button>
                    </div>
                </div>
                <div class="titulo-underline"></div>
                
                <div class="ultimas-mod_container">
                    <p class="titulo-ultimas-mod">Últimas modificaciones</p>
                    <table class="tabla-ultimas-mod">
                        <thead class="encabezado-ultimas-mod">
                            <tr>
                                <td>Fecha</td>
                                <td>Hora</td>
                                <td>Responsable</td>
                                <td>Dpto.</td>
                            </tr>
                        </thead>
                        <tbody class="cuerpo-ultimas-mod">
                            <tr>
                                <td>23/10/24</td>
                                <td>13:00</td>
                                <td>Rafael Castanedo Escobedo</td>
                                <td>Administracion</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="contenedor-dptos-listado">
                <?php foreach(array_slice($departamentos, 0, 8) as $depto): ?>
                    <?= generarDepartamento($depto) ?>
                <?php endforeach; ?>
            </div>

            <div class="contenedor-dptos-listado">
                <?php foreach(array_slice($departamentos, 8, 8) as $depto): ?>
                    <?= generarDepartamento($depto) ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Modal -->
        <div id="modalPersonal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modalTitle">Personal del Departamento</h2>

                <!-- Agregar barra de búsqueda -->
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Buscar personal...">
                </div>

                <!-- Contenedor con scroll para la tabla -->
                <div class="table-container">
                    <table class="tabla-personal">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre Completo</th>
                                <th>Departamento</th>
                                <th>Tipo Plaza</th>
                                <th>Horas Frente Grupo</th>
                                <th>Carga Horaria</th>
                                <th>Horas Definitivas</th>
                                <th>Suma Horas</th>
                                <th>Horas Otros Departamentos</th>
                                <th>Comparación</th>
                            </tr>
                        </thead>
                        <tbody id="tablaBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Obtener elementos del DOM
                const modal = document.getElementById('modalPersonal');
                const span = document.getElementsByClassName('close')[0];
                const modalTitle = document.getElementById('modalTitle');
                const tablaBody = document.getElementById('tablaBody');
                const departamentoCards = document.querySelectorAll('.desglose-button-dpto');

                // Función para abrir el modal
                function openModal(departamento) {
                    modal.style.display = 'block';
                    modalTitle.textContent =
                        departamento === 'todos' ?
                        'Personal de Todos los Departamentos' :
                        `Personal del Departamento ${departamento}`;

                    // Realiza el fetch para obtener los datos del departamento
                    fetchPersonalData(departamento);
                }

                // Función para cerrar el modal
                function closeModal() {
                    modal.style.display = 'none';
                }

                // Función para mostrar mensaje de error
                function showError(message) {
                    tablaBody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: red;">
                        ${message}</td></tr>`;
                }

                // Funcionalidad de búsqueda
                const searchInput = document.getElementById('searchInput');

                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = tablaBody.getElementsByTagName('tr');

                    Array.from(rows).forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });

                // Función para determinar la clase de las horas
                function getHorasClass(actual, requerido) {
                    actual = parseInt(actual) || 0;
                    requerido = parseInt(requerido) || 0;

                    if (actual === 0 && requerido === 0) return 'horas-cero';
                    if (actual < requerido) return 'horas-faltantes';
                    if (actual === requerido) return 'horas-correctas';
                    return 'horas-excedidas';
                }

                // Función para calcular y mostrar los totales
                function agregarFilaTotales() {
                    const tablaBody = document.getElementById('tablaBody');
                    const thead = document.querySelector('.tabla-personal thead');
                    
                    // Eliminar la fila de totales existente si hay una
                    const filaTotalesExistente = document.querySelector('.fila-totales');
                    if (filaTotalesExistente) {
                        filaTotalesExistente.remove();
                    }
                    
                    // Crear una nueva fila para los totales
                    const filaTotales = document.createElement('tr');
                    filaTotales.className = 'fila-totales';
                    
                    // Calcular totales de las columnas numéricas
                    let totalFrenteGrupo = 0;
                    let totalMaxFrenteGrupo = 0;
                    let totalDefinitivas = 0;
                    let totalMaxDefinitivas = 0;
                    let totalTemporales = 0;
                    
                    // Obtener todas las filas de datos (excluyendo la fila de totales)
                    const filas = Array.from(tablaBody.querySelectorAll('tr')).filter(row => !row.classList.contains('fila-totales'));
                    
                    filas.forEach(fila => {
                        // Obtener los valores de las celdas relevantes
                        const celdaFrenteGrupo = fila.querySelector('td:nth-child(7)'); // Horas Frente Grupo
                        const celdaDefinitivas = fila.querySelector('td:nth-child(8)'); // Horas Definitivas
                        const celdaTemporales = fila.querySelector('td:nth-child(9)');  // Horas Temporales
                        
                        // Extraer los valores numéricos
                        if (celdaFrenteGrupo) {
                            const [actual, maximo] = celdaFrenteGrupo.textContent.split('/').map(v => parseFloat(v) || 0);
                            totalFrenteGrupo += actual;
                            totalMaxFrenteGrupo += maximo;
                        }
                        
                        if (celdaDefinitivas) {
                            const [actual, maximo] = celdaDefinitivas.textContent.split('/').map(v => parseFloat(v) || 0);
                            totalDefinitivas += actual;
                            totalMaxDefinitivas += maximo;
                        }
                        
                        if (celdaTemporales) {
                            const valorTemporales = parseFloat(celdaTemporales.textContent) || 0;
                            totalTemporales += valorTemporales;
                        }
                    });
                    
                    // Construir la fila de totales con los totales y máximos redondeados
                    filaTotales.innerHTML = `
                        <td colspan="6" style="text-align: right; font-weight: bold;">Totales:</td>
                        <td style="text-align: center; font-weight: bold;">${Math.round(totalFrenteGrupo)}/${Math.round(totalMaxFrenteGrupo)}</td>
                        <td style="text-align: center; font-weight: bold;">${Math.round(totalDefinitivas)}/${Math.round(totalMaxDefinitivas)}</td>
                        <td style="text-align: center; font-weight: bold;">${Math.round(totalTemporales)}</td>
                    `;
                    
                    // Insertar la fila de totales después del encabezado
                    thead.parentNode.insertBefore(filaTotales, thead.nextSibling);
                }

                // Función para obtener los datos del personal
                function fetchPersonalData(departamento) {
                    // Limpiar la búsqueda al cargar nuevos datos
                    searchInput.value = '';

                    // Mostrar mensaje de carga
                    const colSpan = 11; // Ajustado para incluir la nueva columna de horas totales
                    tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center;">Cargando...</td></tr>`;

                    fetch('./functions/horas-comparacion/obtener-personal.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `departamento=${encodeURIComponent(departamento)}`
                        })
                        .then(response => response.text())
                        .then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Error parsing JSON:', text);
                                throw new Error('Error al procesar la respuesta del servidor');
                            }
                        })
                        .then(data => {
                            if (data.error) {
                                throw new Error(data.error);
                            }

                            if (!Array.isArray(data) || data.length === 0) {
                                tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center;">No se encontraron datos para mostrar</td></tr>`;
                                return;
                            }

                            // Limpiar la tabla y agregar los datos
                            tablaBody.innerHTML = '';

                            //Encabezados de la tabla
                            const thead = document.querySelector('.tabla-personal thead tr');
                                thead.innerHTML = `
                                    <th>Código</th>
                                    <th>Nombre Completo</th>
                                    <th>Departamento</th>
                                    <th>Categoría Actual</th>
                                    <th>Tipo Plaza</th>
                                    <th>Carga Horaria</th>
                                    <th>Horas Frente Grupo</th>
                                    <th>Horas Definitivas</th>
                                    <th>Horas Temporales</th>
                                `;
                            tablaBody.innerHTML = ''; // Limpiar tabla

                            // Obtener el color del departamento
                            function getDepartmentColor(departamento) {
                                const normalizedDept = departamento.toLowerCase()
                                    .normalize("NFD")
                                    .replace(/[\u0300-\u036f]/g, "")
                                    .replace(/[^a-z\s]/g, "")
                                    .trim();
                                
                                // Mapping de departamentos a clases CSS
                                const classMapping = {
                                    'administracion': 'dept-administracion',
                                    'programa de aprendizaje de lengua extranjera': 'dept-pale',
                                    'pale': 'dept-pale',
                                    'auditoria': 'dept-auditoria',
                                    'secretaria administrativa': 'dept-auditoria',
                                    'ciencias sociales': 'dept-ciencias-sociales',
                                    'politicas publicas': 'dept-politicas-publicas',
                                    'contabilidad': 'dept-contabilidad',
                                    'economia': 'dept-economia',
                                    'estudios regionales': 'dept-estudios-regionales',
                                    'finanzas': 'dept-finanzas',
                                    'impuestos': 'dept-impuestos',
                                    'mercadotecnia': 'dept-mercadotecnia',
                                    'metodos cuantitativos': 'dept-metodos-cuantitativos',
                                    'recursos humanos': 'dept-recursos-humanos',
                                    'sistemas de informacion': 'dept-sistemas',
                                    'turismo': 'dept-turismo'
                                };

                                // Buscar coincidencia
                                for (let [key, value] of Object.entries(classMapping)) {
                                    if (normalizedDept.includes(key)) {
                                        return value;
                                    }
                                }
                                
                                return 'dept-otros'; // Valor por defecto
                            }

                            data.forEach(persona => {
                                const row = document.createElement('tr');

                                // Función para obtener la clase del departamento
                                function getDepartamentoClass(departamento) {
                                    // Normalizar el texto del departamento
                                    const normalizedDept = departamento.toLowerCase()
                                        .normalize("NFD")
                                        .replace(/[\u0300-\u036f]/g, "")
                                        .replace(/[^a-z\s]/g, "")
                                        .trim();

                                    const mapping = {
                                        'administracion': 'administracion',
                                        'programa de aprendizaje de lengua extranjera': 'pale',
                                        'pale': 'pale',
                                        'administracion/programa de aprendizaje de lengua extranjera': 'pale',
                                        'auditoria': 'auditoria',
                                        'secretaria administrativa': 'auditoria',
                                        'ciencias sociales': 'ciencias-sociales',
                                        'politicas publicas': 'politicas-publicas',
                                        'contabilidad': 'contabilidad',
                                        'economia': 'economia',
                                        'estudios regionales': 'estudios-regionales',
                                        'finanzas': 'finanzas',
                                        'impuestos': 'impuestos',
                                        'mercadotecnia': 'mercadotecnia',
                                        'metodos cuantitativos': 'metodos-cuantitativos',
                                        'recursos humanos': 'recursos-humanos',
                                        'sistemas de informacion': 'sistemas-informacion',
                                        'turismo': 'turismo'
                                    };

                                    // Buscar coincidencia exacta primero
                                    for (let [key, value] of Object.entries(mapping)) {
                                        if (normalizedDept === key) {
                                            return value;
                                        }
                                    }

                                    // Si no hay coincidencia exacta, buscar coincidencia parcial
                                    for (let [key, value] of Object.entries(mapping)) {
                                        // Para PALE, buscar coincidencias específicas
                                        if (value === 'pale' &&
                                            (normalizedDept.includes('pale') ||
                                                normalizedDept.includes('programa de aprendizaje') ||
                                                normalizedDept.includes('lengua extranjera'))) {
                                            return 'pale';
                                        }
                                        if (normalizedDept.includes(key)) {
                                            return value;
                                        }
                                    }

                                    return 'default';
                                }

                                    function formatTooltipContent(departmentData) {
                                        if (!departmentData || departmentData.trim() === '') {
                                            return 'No hay información de departamentos disponible';
                                        }
                                        
                                        // Split by newlines and format each line
                                        const departments = departmentData.split('\n').filter(line => line.trim());
                                        return departments.join('<br>');
                                    }

                                // Función para formatear las horas por departamento
                                function formatearHorasDepartamento(horasString, tipoHoras) {
                                    if (!horasString || horasString.trim() === '') {
                                        return '';
                                    }

                                    let formattedHoras = '';
                                    let horasArray = horasString.split('\n');

                                    for (let i = 0; i < horasArray.length; i++) {
                                        let linea = horasArray[i].trim();
                                        if (linea === '') continue; // Saltar líneas vacías

                                        // Dividir por el primer ':' solamente
                                        const [dept, horas] = linea.split(/:(.+)/).map(s => s?.trim()).filter(Boolean);
                                        if (!dept || !horas) continue; // Saltar si falta departamento u horas

                                        const [horasActual, horasRequeridas] = horas.split('/').map(h => parseInt(h.trim()));

                                        // Si las horas son 0/0, no mostrar la burbuja
                                        if (horasActual === 0 && horasRequeridas === 0) {
                                            continue;
                                        }

                                        const horasClass = getHorasClass(horasActual, tipoHoras === 'definitivas' ? parseInt(persona.Horas_definitivas) : parseInt(persona.Horas_frente_grupo));

                                        formattedHoras += `
                                            <div class="departamento-tag tag-${getDepartamentoClass(dept)} ${horasClass}" style="position: relative; display: inline-block; max-width: 100%;">
                                                ${dept}: ${horas}
                                            </div>
                                        `;
                                    }

                                    return formattedHoras;
                                }

                                // Procesar horas frente a grupo
                                const horasCargoActual = persona.suma_cargo_plaza || 0;
                                const horasFrenteRequeridas = persona.Horas_frente_grupo || 0;
                                const claseFrenteGrupo = getHorasClass(horasCargoActual, horasFrenteRequeridas);

                                // Procesar horas definitivas
                                const horasDefActual = persona.suma_horas_definitivas || 0;
                                const horasDefRequeridas = persona.Horas_definitivas || 0;
                                const claseDefinitivas = getHorasClass(horasDefActual, horasDefRequeridas);

                                // Procesar horas temporales
                                const horasTemporales = persona.suma_horas_temporales || 0;
                                

                                // Determinar la clase del departamento para asignar el color a horas temporales
                                const deptClass = getDepartmentColor(persona.Departamento || 'otros');
                                
                                const horasFrenteGrupoHTML = `
                                    <div class="tooltip">
                                        <span class="${claseFrenteGrupo}">${horasCargoActual}/${horasFrenteRequeridas}</span>
                                        <div class="tooltiptext">${persona.horas_cargo_por_departamento ? 
                                            persona.horas_cargo_por_departamento.replace(/\n/g, '<br>').replace(/<br>/g, '<br>') : 
                                            ''}
                                        </div>
                                    </div>
                                `;

                                const horasDefinitivasHTML = `
                                    <div class="tooltip">
                                        <span class="${claseDefinitivas}">${horasDefActual}/${horasDefRequeridas}</span>
                                        <div class="tooltiptext">${persona.horas_definitivas_por_departamento ? 
                                            persona.horas_definitivas_por_departamento.replace(/\n/g, '<br>').replace(/<br>/g, '<br>') : 
                                            ''}
                                        </div>
                                    </div>
                                `;
                                
                                // Horas temporales con el color del departamento
                                const horasTemporalesHTML = `
                                    <div class="tooltip" style="text-align: center; width: 100%;">
                                        <span class="${deptClass}" style="display: inline-block;">${horasTemporales}</span>
                                        <div class="tooltiptext">${persona.horas_temporales_por_departamento || ''}</div>
                                    </div>
                                `;

                                // Generar el contenido del tooltip para horas totales
                                function generarDesgloseTotalHoras(persona) {
                                    // Combinar todas las horas por departamento
                                    const departamentos = new Map();
                                    
                                    // Procesar horas de cargo
                                    if (persona.horas_cargo_por_departamento) {
                                        persona.horas_cargo_por_departamento.split('\n').forEach(linea => {
                                            if (linea.trim() === '') return;
                                            const [dept, horas] = linea.split(/:(.+)/).map(s => s?.trim()).filter(Boolean);
                                            if (!dept || !horas) return;
                                            
                                            const horasActual = parseInt(horas.split('/')[0]);
                                            if (departamentos.has(dept)) {
                                                departamentos.set(dept, departamentos.get(dept) + horasActual);
                                            } else {
                                                departamentos.set(dept, horasActual);
                                            }
                                        });
                                    }
                                    
                                    // Procesar horas definitivas
                                    if (persona.horas_definitivas_por_departamento) {
                                        persona.horas_definitivas_por_departamento.split('\n').forEach(linea => {
                                            if (linea.trim() === '') return;
                                            const [dept, horas] = linea.split(/:(.+)/).map(s => s?.trim()).filter(Boolean);
                                            if (!dept || !horas) return;
                                            
                                            const horasActual = parseInt(horas.split('/')[0]);
                                            if (departamentos.has(dept)) {
                                                departamentos.set(dept, departamentos.get(dept) + horasActual);
                                            } else {
                                                departamentos.set(dept, horasActual);
                                            }
                                        });
                                    }
                                    
                                    // Procesar horas temporales
                                    if (persona.horas_temporales_por_departamento) {
                                        persona.horas_temporales_por_departamento.split('\n').forEach(linea => {
                                            if (linea.trim() === '') return;
                                            const [dept, horas] = linea.split(/:(.+)/).map(s => s?.trim()).filter(Boolean);
                                            if (!dept || !horas) return;
                                            
                                            const horasActual = parseInt(horas);
                                            if (departamentos.has(dept)) {
                                                departamentos.set(dept, departamentos.get(dept) + horasActual);
                                            } else {
                                                departamentos.set(dept, horasActual);
                                            }
                                        });
                                    }
                                    
                                    // Generar el texto del tooltip
                                    let tooltipText = '';
                                    departamentos.forEach((horas, dept) => {
                                        if (horas > 0) {
                                            tooltipText += `${dept}: ${horas}\n`;
                                        }
                                    });
                                    
                                    return tooltipText || 'No hay desglose disponible';
                                }

                                const tooltipDesgloseTotalHoras = generarDesgloseTotalHoras(persona);

                                const tdContent = `
                                    <td>${persona.Codigo || ''}</td>
                                    <td>${persona.Nombre_completo || ''}</td>
                                    <td>${persona.Departamento || ''}</td>
                                    <td>${persona.Categoria_actual || ''}</td>
                                    <td>${persona.Tipo_plaza || ''}</td>
                                    <td>${persona.Carga_horaria || ''}</td>
                                    <td>${horasFrenteGrupoHTML}</td>
                                    <td>${horasDefinitivasHTML}</td>
                                    <td>${horasTemporalesHTML}</td>
                                `;

                                row.innerHTML = tdContent;
                                tablaBody.appendChild(row);
                            });
                            agregarFilaTotales();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center; color: red;">
                            ${error.message || 'Error al cargar los datos'}</td></tr>`;
                        });
                    }

                // Event Listeners
                departamentoCards.forEach(card => {
                    card.addEventListener('click', function() {
                        const departamento = this.getAttribute('data-departamento'); // Obtener el atributo correctamente
                        openModal(departamento); // Pasar el valor al modal
                    });
                });

                const botonTodos = document.getElementById('desglose-todos');
                botonTodos.addEventListener('click', function() {
                    openModal('todos'); // Llama a la función con 'todos' como parámetro
                });

                span.onclick = closeModal;

                window.onclick = function(event) {
                    if (event.target == modal) {
                        closeModal();
                    }
                }
            });

            /* Script para desplegable de departamentos */
            let contenedorActual = null;
            let iconoActual = null;

            function mostrarInformacion(contenedorId, icono, esArriba = false) {
                const nuevoContenedor = document.getElementById(contenedorId);

                if (contenedorActual && contenedorActual !== nuevoContenedor) {
                    contenedorActual.classList.remove('mostrar');
                    contenedorActual.classList.remove('desplegable-arriba');

                    // Remover clases de rotacion del icono anterior
                    iconoActual.classList.remove('rotar');
                    iconoActual.classList.remove('rotar-arriba');
                }

                // Alternar mostrar/ocultar
                nuevoContenedor.classList.toggle('mostrar');

                // Manejar la rotación segun la dirección
                if (esArriba) {
                    // Si es hacia arriba y se está mostrando
                    if (nuevoContenedor.classList.contains('mostrar')) {
                        nuevoContenedor.classList.add('desplegable-arriba');
                        icono.classList.add('rotar-arriba');
                    } else {
                        // Si se está ocultando, quitar las clases
                        nuevoContenedor.classList.remove('desplegable-arriba');
                        icono.classList.remove('rotar-arriba');
                    }
                } else {
                    // Para contenedores hacia abajo
                    icono.classList.toggle('rotar');
                }

                if (nuevoContenedor.classList.contains('mostrar')) {
                    contenedorActual = nuevoContenedor;
                    iconoActual = icono;
                } else {
                    contenedorActual = null;
                    iconoActual = null;
                }
            }
        </script>

        <?php include("./template/footer.php"); ?>  