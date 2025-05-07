<?php
session_start();

// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 3 && $_SESSION['Rol_ID'] != 0) {
    header("Location: home.php");
    exit();
}
?>

<?php include './template/header.php' ?>
<?php include './template/navbar.php' ?>
<?php
require_once './config/db.php';

// Obtener el total de horas asignadas
function getTotalAssignedHours($conexion, $codigo_profesor) {
    $tablas_departamentos = [
        'data_administración',
        'data_auditoría',
        'data_ciencias_sociales',
        'data_contabilidad',
        'data_economía',
        'data_estudios_regionales',
        'data_finanzas',
        'data_impuestos',
        'data_mercadotecnia',
        'data_métodos_cuantitativos',
        'data_pale',
        'data_políticas_públicas',
        'data_posgrados',
        'data_recursos_humanos',
        'data_sistemas_de_información',
        'data_turismo'
    ];

    $total_horas = 0;

    foreach ($tablas_departamentos as $tabla) {
        $sql = "SELECT SUM(CAST(HORAS AS UNSIGNED)) AS horas 
                FROM $tabla 
                WHERE CODIGO_PROFESOR = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $codigo_profesor);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_horas += $row['horas'] ? intval($row['horas']) : 0;
        $stmt->close();
    }

    return $total_horas;
}

// Consulta SQL para obtener solo registros activos
$tabla_departamento = "coord_per_prof";
$sql = "SELECT * FROM $tabla_departamento WHERE Papelera = 'activo'";
$result = mysqli_query($conexion, $sql);

// Preparar datos para AG Grid
$rowData = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Aquí podrías añadir información de choques o cálculos adicionales
    // Por ejemplo:
    // $row['choques'] = calcularChoques($conexion, $row['Codigo']);
    
    // Incluir las horas asignadas para cada profesor si es necesario
    // $row['total_horas_asignadas'] = getTotalAssignedHours($conexion, $row['Codigo']);
    
    $rowData[] = $row;
}
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.1.0/styles/ag-grid.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.1.0/styles/ag-theme-alpine.css">

<title>Coordinación de Personal - Plantilla Académica</title>

<!-- Script de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Mostrar el loader inmediatamente
    window.addEventListener('load', function() {
        Swal.close();
    });

    Swal.fire({
        title: 'Cargando datos...',
        html: 'Por favor espere mientras se procesan los datos',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
</script>

<!-- Tabulator CSS Plugins -->
<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
<!-- Tabulator JS -->
<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>

<!-- CSS base -->
<link rel="stylesheet" href="./CSS/coord-pers/basesdedatos-Coord.css?v=<?php echo filemtime('./CSS/coord-pers/basesdedatos-Coord.css'); ?>">
<link rel="stylesheet" href="./CSS/coord-pers/coord-papelera.css?v=<?php echo filemtime('./CSS/coord-pers/coord-papelera.css'); ?>">
<link rel="stylesheet" href="./CSS/coord-pers/modal-anadir-registro.css?v=<?php echo filemtime('./CSS/coord-pers/modal-anadir-registro.css'); ?>">
<link rel="stylesheet" href="./CSS/coord-pers/ag-grid-test.css?v=<?php echo filemtime('./CSS/coord-pers/test.css'); ?>">

<<<<<<< HEAD
=======
<!-- Agregamos un estilo específico para AG Grid -->
<style>
    .ag-theme-alpine {
        --ag-header-height: 40px;
        --ag-header-background-color: #f8f9fa;
        --ag-row-hover-color: #e9ecef;
        --ag-selected-row-background-color: rgba(33, 150, 243, 0.1);
    }
    
    /* Estilo para celdas con choque */
    .celda-choque {
        background-color: rgba(255, 0, 0, 0.2) !important;
    }
    
    /* Tooltip personalizado */
    .ag-tooltip {
        position: absolute;
        background: #333;
        color: #fff;
        border-radius: 3px;
        padding: 5px;
        z-index: 1000;
        max-width: 200px;
    }
    
    /* Asegurar que el contenedor de la tabla permite scroll horizontal */
    #myGrid {
        overflow-x: auto !important;
        width: 100% !important;
    }
    
    /* Forzar ancho mínimo para el contenedor de la tabla */
    .ag-root-wrapper {
        min-width: 100% !important;
    }
    
    /* Asegurar que las columnas pinned se mantienen visibles */
    .ag-pinned-left-cols-container {
        z-index: 10;
    }
</style>
>>>>>>> 894005f (Avances nueva libreria AG Grid)

<div class="cuadro-principal">
    <div class="cuadro-scroll">
        <div class="encabezado">
            <div class="encabezado-izquierda" style="display: flex; align-items: center;">
                <!-- Búsqueda rápida personalizada -->
                <div class="campo-busqueda">
                    <input type="text" id="quickFilter" placeholder="Buscar..." onInput="onQuickFilterChanged()">
                    <i aria-hidden="true"></i>
                </div>
            </div>
            <div class="encabezado-centro">
                <h3>Plantilla Académica - Coordinación de Personal</h3>
            </div>
            <div class="encabezado-derecha">
                <div class="iconos-container">
                    <div class="icono-buscador" id="icono-guardar" onclick="saveAllChanges()" data-tooltip="Guardar cambios">
                        <i class="fa fa-save" aria-hidden="true"></i>
                    </div>
                    <div class="icono-buscador" id="icono-deshacer" onclick="undoAllChanges()" data-tooltip="Deshacer cambios">
                        <i class="fa fa-undo" aria-hidden="true"></i>
                    </div>
                    <div class="icono-buscador" id="icono-papelera" onclick="mostrarModalRegistrosEliminados()" data-tooltip="Ver registros eliminados">
                        <i class="fa fa-trash-restore" aria-hidden="true"></i>
                    </div>
                    <div class="icono-buscador" id="icono-visibilidad" onclick="toggleColumnVisibility()" data-tooltip="Mostrar/ocultar columnas">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </div>
                    <div class="icono-buscador" id="icono-filtro" onclick="toggleFilter()" data-tooltip="Mostrar/ocultar filtros">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                    </div>
                    <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()" data-tooltip="Añadir nuevo registro">
                        <i class="fa fa-add" aria-hidden="true"></i>
                    </div>
                    <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()" data-tooltip="Eliminar registros seleccionados">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </div>
                    <div class="icono-buscador" id="icono-descargar" onclick="exportToExcel()" data-tooltip="Descargar Excel">
                        <i class="fa fa-download" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>

        <div id="myGrid" class="ag-theme-alpine" style="height: 620px; width: 100%;"></div>
    </div>
</div>

<!-- Modales incluidos -->
<?php include './functions/coord-personal-plantilla/modal-descargar-excel/modal-descargar-excel.php'; ?>
<?php include './functions/coord-personal-plantilla/modal-anadir-registro/modal-anadir-registro.php'; ?>
<?php include './functions/coord-personal-plantilla/registros-eliminados/modal-registros-eliminados-cp.php'; ?>

<<<<<<< HEAD
    <div class="Tabla datatable-container">
        <div class="table-container">
            <div id="tabla-datos-tabulator"></div> <!-- Solo este elemento es necesario para Tabulator -->
=======
<!-- Modal para selección de columnas visibles -->
<div class="modal fade" id="columnVisibilityModal" tabindex="-1" aria-labelledby="columnVisibilityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnVisibilityModalLabel">Visibilidad de columnas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="columnVisibilityList">
                <!-- Se llenará dinámicamente desde JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="applyColumnVisibility()">Aplicar</button>
            </div>
>>>>>>> 894005f (Avances nueva libreria AG Grid)
        </div>
    </div>
</div>

<!-- Linea que valida el rol id del usuario para mandarlo a JS -->
<input type="hidden" id="user-role" value="<?php echo $_SESSION['Rol_ID']; ?>">

<!-- jQuery y Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AG Grid -->
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@31.1.0/dist/ag-grid-community.min.js"></script>

<!-- Scripts personalizados-->
<script src="./JS/plantilla-CoordPers/eliminar-registro-coord.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/eliminar-registro-coord.js'); ?>"></script>
<script src="./JS/plantilla-CoordPers/modal-eliminados-coord-pers.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/modal-eliminados-coord-pers.js'); ?>"></script>
<script src="./JS/plantilla-CoordPers/anadir-profesor.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/anadir-profesor.js'); ?>"></script>
<script src="./JS/plantilla-CoordPers/descargar-data-excel-coord.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/descargar-data-excel-coord.js'); ?>"></script>

<<<<<<< HEAD
    <!-- Scripts personalizados-->
    <!-- <script src="./JS/plantilla-CoordPers/tabla-editable-coord.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/tabla-editable-coord.js'); ?>"></script> -->
    <script src="./JS/plantilla-CoordPers/eliminar-registro-coord.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/eliminar-registro-coord.js'); ?>"></script>
    <script src="./JS/plantilla-CoordPers/modal-eliminados-coord-pers.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/modal-eliminados-coord-pers.js'); ?>"></script>
    <script src="./JS/plantilla-CoordPers/anadir-profesor.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/anadir-profesor.js'); ?>"></script>
    <script src="./JS/plantilla-CoordPers/descargar-data-excel-coord.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/descargar-data-excel-coord.js'); ?>"></script>
    <script src="./JS/plantilla-CoordPers/inicializar-tablas-cp.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/inicializar-tablas-cp.js'); ?>"></script>
=======
<script>
// Variable global para la grid
let gridApi;
let columnApi;
let changedData = {}; // Para almacenar cambios pendientes

// Configuración principal de AG Grid
document.addEventListener('DOMContentLoaded', function() {
    const userRole = document.getElementById('user-role').value;
    const isEditable = userRole == 3 || userRole == 0;
    
    // Definir columnas
    const columnDefs = [
        {
            headerName: 'ID',
            field: 'ID',
            checkboxSelection: true,
            headerCheckboxSelection: true,
            width: 50,
            sortable: false,
            filter: false,
            resizable: false,
            pinned: 'left'
        },
        { 
            headerName: 'DATOS', 
            field: 'Datos', 
            width: 100,
            filter: 'agTextColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            }
        },
        { 
            headerName: 'CODIGO', 
            field: 'Codigo', 
            width: 100,
            filter: 'agTextColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            }
        },
        { 
            headerName: 'PATERNO', 
            field: 'Paterno', 
            width: 120,
            filter: 'agTextColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            }
        },
        { 
            headerName: 'MATERNO', 
            field: 'Materno', 
            width: 120,
            filter: 'agTextColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            }
        },
        { 
            headerName: 'NOMBRES', 
            field: 'Nombres', 
            width: 150,
            filter: 'agTextColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            }
        },
        { 
            headerName: 'NOMBRE COMPLETO', 
            field: 'Nombre_completo', 
            width: 200,
            filter: 'agTextColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            }
        },
        { 
            headerName: 'DEPARTAMENTO', 
            field: 'Departamento', 
            width: 150,
            filter: 'agTextColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            }
        },
        // Columna editable con reglas de estilo condicional
        {
            headerName: 'CATEGORIA ACTUAL', 
            field: 'Categoria_actual',
            editable: isEditable,
            width: 150,
            filter: 'agNumberColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            },
            cellClassRules: {
                'celda-choque': params => {
                    // Lógica para resaltar celdas con choques
                    return params.data.choques && params.data.choques.length > 0;
                }
            },
            // Validador de celdas (solo números)
            valueParser: params => {
                const newValue = Number(params.newValue);
                return isNaN(newValue) ? params.oldValue : newValue;
            }
        },
        {
            headerName: 'CATEGORIA ACTUAL',
            field: 'Categoria_actual_dos',
            editable: isEditable,
            width: 150,
            filter: 'agTextColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            },
        },
        {
            headerName: 'HORAS FRENTE A GRUPO',
            field: 'Horas_frente_grupo',
            editable: isEditable,
            width: 150,
            filter: 'agNumberColumnFilter',
            filterParams: {
                buttons: ['reset', 'apply'],
                closeOnApply: true
            },
        },
        {
        headerName: 'DIVISION',
        field: 'Division',
        width: 180,
        filter: 'agTextColumnFilter',
        filterParams: {
            buttons: ['reset', 'apply'],
            closeOnApply: true
        }
    },
    {
        headerName: 'TIPO DE PLAZA',
        field: 'Tipo_plaza',
        width: 220,
        filter: 'agTextColumnFilter'
    },
    {
        headerName: 'CAT.ACT.',
        field: 'Cat_act',
        width: 100,
        filter: 'agTextColumnFilter'
    },
    {
        headerName: 'CARGA HORARIA',
        field: 'Carga_horaria',
        width: 120,
        filter: 'agTextColumnFilter'
    },
    {
        headerName: 'HORAS DEFINITIVAS',
        field: 'Horas_definitivas',
        width: 150,
        filter: 'agNumberColumnFilter',
        editable: isEditable,
        valueParser: params => {
            const newValue = Number(params.newValue);
            return isNaN(newValue) ? params.oldValue : newValue;
        }
    },
    {
        headerName: 'UDG VIRTUAL CIT OTRO CENTRO',
        field: 'Udg_virtual_CIT',
        width: 200,
        filter: 'agTextColumnFilter'
    },
    {
        headerName: 'HORARIO',
        field: 'Horario',
        width: 180,
        filter: 'agTextColumnFilter'
    },
    {
        headerName: 'TURNO',
        field: 'Turno',
        width: 80,
        filter: 'agTextColumnFilter'
    },
    {
        headerName: 'INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION',
        field: 'Investigacion_nombramiento_cambio_funcion',
        width: 300,
        filter: 'agTextColumnFilter',
        tooltipField: 'Investigacion_nombramiento_cambio_funcion'
    },
    {
        headerName: 'S.N.I.',
        field: 'SNI',
        width: 80,
        filter: 'agTextColumnFilter'
    },
    {
        headerName: 'SNI DESDE',
        field: 'SNI_desde',
        width: 120,
        filter: 'agTextColumnFilter'
    },
    {
        headerName: 'CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR',
        field: 'Cambio_dedicacion',
        width: 300,
        filter: 'agTextColumnFilter'
    },
    {
        headerName: 'TELEFONO PARTICULAR',
        field: 'Telefono_particular',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'TELEFONO OFICINA O CELULAR',
        field: 'Telefono_oficina',
        width: 180,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'DOMICILIO',
        field: 'Domicilio',
        width: 200,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'COLONIA',
        field: 'Colonia',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'C.P.',
        field: 'CP',
        width: 80,
        filter: 'agNumberColumnFilter',
        editable: isEditable,
        valueParser: params => {
            const newValue = Number(params.newValue);
            return isNaN(newValue) ? params.oldValue : newValue;
        }
    },
    {
        headerName: 'CIUDAD',
        field: 'Ciudad',
        width: 120,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'ESTADO',
        field: 'Estado',
        width: 120,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'NO. AFIL. I.M.S.S.',
        field: 'No_imss',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'C.U.R.P.',
        field: 'CURP',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'RFC',
        field: 'RFC',
        width: 100,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'LUGAR DE NACIMIENTO',
        field: 'Lugar_nacimiento',
        width: 180,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'ESTADO CIVIL',
        field: 'Estado_civil',
        width: 100,
        filter: 'agTextColumnFilter',
        editable: isEditable,
        cellEditor: 'agSelectCellEditor',
        cellEditorParams: {
            values: ['Soltero', 'Casado', 'Divorciado', 'Viudo', 'Unión Libre']
        }
    },
    {
        headerName: 'TIPO DE SANGRE',
        field: 'Tipo_sangre',
        width: 120,
        filter: 'agTextColumnFilter',
        editable: isEditable,
        cellEditor: 'agSelectCellEditor',
        cellEditorParams: {
            values: ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']
        }
    },
    {
        headerName: 'FECHA NAC.',
        field: 'Fecha_nacimiento',
        width: 120,
        filter: 'agDateColumnFilter',
        editable: isEditable,
        cellEditor: 'agDateStringCellEditor'
    },
    {
        headerName: 'EDAD',
        field: 'Edad',
        width: 80,
        filter: 'agNumberColumnFilter',
        editable: false // No editable, se calcula automáticamente
    },
    {
        headerName: 'NACIONALIDAD',
        field: 'Nacionalidad',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'CORREO ELECTRÓNICO',
        field: 'Correo',
        width: 200,
        filter: 'agTextColumnFilter',
        editable: isEditable,
        valueParser: params => {
            // Validación básica de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(params.newValue) ? params.newValue : params.oldValue;
        }
    },
    {
        headerName: 'CORREOS OFICIALES',
        field: 'Correos_oficiales',
        width: 200,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'ULTIMO GRADO',
        field: 'Ultimo_grado',
        width: 120,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'PROGRAMA',
        field: 'Programa',
        width: 200,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'NIVEL',
        field: 'Nivel',
        width: 80,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'INSTITUCION',
        field: 'Institucion',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'ESTADO/PAIS',
        field: 'Estado_pais',
        width: 120,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'AÑO',
        field: 'Año',
        width: 80,
        filter: 'agNumberColumnFilter',
        editable: isEditable,
        valueParser: params => {
            const newValue = Number(params.newValue);
            return isNaN(newValue) ? params.oldValue : newValue;
        }
    },
    {
        headerName: 'GDO EXP',
        field: 'Gdo_exp',
        width: 120,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'OTRO GRADO',
        field: 'Otro_grado_alternativo',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'PROGRAMA',
        field: 'Otro_programa_alternativo',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'NIVEL',
        field: 'Otro_nivel_altenrativo',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'INSTITUCION',
        field: 'Otro_institucion_alternativo',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'ESTADO/PAIS',
        field: 'Otro_estado_pais_alternativo',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'AÑO',
        field: 'Otro_año_alternativo',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'GDO EXP',
        field: 'Otro_gdo_exp_alternativo',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'PROESDE 24-25',
        field: 'Proesde_24_25',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'A PARTIR DE',
        field: 'A_partir_de',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'FECHA DE INGRESO',
        field: 'Fecha_ingreso',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },
    {
        headerName: 'ANTIGÜEDAD',
        field: 'Antiguedad',
        width: 150,
        filter: 'agTextColumnFilter',
        editable: isEditable
    },

    ];

    // Configuración de la grid
    const gridOptions = {
        columnDefs: columnDefs,
        rowData: <?php echo json_encode($rowData); ?>,
        defaultColDef: {
            autoHeaderHeight: true,
            sortable: true,
            filter: true,
            resizable: true,
            editable: isEditable,
            floatingFilter: false, // Se activa con el botón de filtro
            suppressMenu: false,   // Mostrar menú de columna
            minWidth: 80
        },
        rowSelection: 'multiple',
        pagination: true,
        paginationPageSize: 15,
        paginationAutoPageSize: false,
        suppressRowClickSelection: true,
        animateRows: true,
        enableCellTextSelection: true,
        ensureDomOrder: true,
        rowHeight: 40,
        suppressColumnVirtualisation: true, // Impide que las columnas se virtualicen (oculten)
        enableRangeSelection: true, // Permite selección de rangos de celdas
        // Configuración del fill handle (arrastrar para copiar)
        cellSelection: {
            handle: {
                mode: 'fill'
            }
        },
        fillHandleDirection: 'y', // Solo permitir copiar hacia abajo (eje y)
        onGridReady: function(params) {
            // Ajusta automáticamente el tamaño de todas las columnas según su contenido
            params.api.autoSizeAllColumns();
            gridApi = params.api;
            columnApi = params.columnApi;
            
            // No ajustamos columnas automáticamente al tamaño disponible
            // para permitir scroll horizontal cuando hay muchas columnas
            
            // Cerrar el loader
            Swal.close();
            
            // Configurar tooltips para celdas con errores/choques
            params.api.addEventListener('cellMouseOver', function(event) {
                if (event.colDef.field === 'Horas_frente_grupo' && event.data.choques && event.data.choques.length > 0) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'ag-tooltip';
                    tooltip.innerHTML = 'Choques: ' + event.data.choques.join(', ');
                    event.event.target.appendChild(tooltip);
                }
            });
            
            params.api.addEventListener('cellMouseOut', function(event) {
                const tooltip = event.event.target.querySelector('.ag-tooltip');
                if (tooltip) {
                    tooltip.remove();
                }
            });
        },
        onCellValueChanged: function(event) {
            // Guardar cambios cuando se edita una celda
            if (event.oldValue !== event.newValue) {
                // Almacenar cambio en el objeto de cambios pendientes
                if (!changedData[event.data.ID]) {
                    changedData[event.data.ID] = {};
                }
                changedData[event.data.ID][event.colDef.field] = event.newValue;
                
                // Opcional: Mostrar un indicador de cambios pendientes
                const saveButton = document.getElementById('icono-guardar');
                saveButton.classList.add('has-changes');
                
                // También se puede guardar inmediatamente si se prefiere
                // saveCellChange(event.data.ID, event.colDef.field, event.newValue);
            }
        },
        // Componente personalizado para el filtro rápido (buscar en todas las columnas)
        isExternalFilterPresent: function() {
            return document.getElementById('quickFilter').value !== '';
        },
        doesExternalFilterPass: function(node) {
            const filterText = document.getElementById('quickFilter').value.toLowerCase();
            if (!filterText) return true;
            
            // Buscar en todas las columnas visibles
            const rowData = node.data;
            return Object.keys(rowData).some(key => {
                const value = rowData[key];
                return value && value.toString().toLowerCase().includes(filterText);
            });
        }
    };
>>>>>>> 894005f (Avances nueva libreria AG Grid)

    // Crear la grid
    new agGrid.Grid(document.getElementById('myGrid'), gridOptions);

    // Redimensionar cuando cambia el tamaño de la ventana, pero respetando el ancho mínimo de columnas
    window.addEventListener('resize', function() {
        if (gridApi) {
            // Calculamos el ancho total de todas las columnas
            let totalWidth = 0;
            columnApi.getAllColumns().forEach(col => {
                totalWidth += col.getActualWidth();
            });
            
            // Solo ajustamos automáticamente si el ancho total es menor que el contenedor
            const containerWidth = document.getElementById('myGrid').offsetWidth;
            if (totalWidth < containerWidth) {
                gridApi.sizeColumnsToFit();
            }
        }
    });
});

// Función para el filtro rápido
function onQuickFilterChanged() {
    gridApi.onFilterChanged();
}

// Función para alternar la visibilidad de las columnas
function toggleColumnVisibility() {
    // Crear el contenido del modal dinámicamente
    const columnVisibilityList = document.getElementById('columnVisibilityList');
    columnVisibilityList.innerHTML = '';
    
    // Obtener todas las columnas y su estado actual
    columnApi.getAllColumns().forEach(column => {
        if (column.getColId() !== '') { // Excluir columna de selección
            const isVisible = column.isVisible();
            const checkbox = document.createElement('div');
            checkbox.className = 'form-check';
            checkbox.innerHTML = `
                <input class="form-check-input column-visibility-checkbox" type="checkbox" 
                       value="${column.getColId()}" id="col-${column.getColId()}" 
                       ${isVisible ? 'checked' : ''}>
                <label class="form-check-label" for="col-${column.getColId()}">
                    ${column.getColDef().headerName || column.getColId()}
                </label>
            `;
            columnVisibilityList.appendChild(checkbox);
        }
    });
    
    // Mostrar el modal
    const columnVisibilityModal = new bootstrap.Modal(document.getElementById('columnVisibilityModal'));
    columnVisibilityModal.show();
}

// Aplicar cambios de visibilidad de columnas
function applyColumnVisibility() {
    const checkboxes = document.querySelectorAll('.column-visibility-checkbox');
    const columnsToShow = [];
    const columnsToHide = [];
    
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            columnsToShow.push(checkbox.value);
        } else {
            columnsToHide.push(checkbox.value);
        }
    });
    
    // Actualizar visibilidad
    columnApi.setColumnsVisible(columnsToShow, true);
    columnApi.setColumnsVisible(columnsToHide, false);
    
    // Cerrar el modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('columnVisibilityModal'));
    modal.hide();
}

// Alternar filtros
function toggleFilter() {
    const currentValue = gridApi.getFilterModel() ? Object.keys(gridApi.getFilterModel()).length > 0 : false;
    
    if (currentValue) {
        // Si hay filtros activos, limpiarlos
        gridApi.setFilterModel(null);
        document.getElementById('icono-filtro').classList.remove('active');
    } else {
        // Activar filtros flotantes
        columnApi.getColumns().forEach(column => {
            const colDef = column.getColDef();
            if (colDef.filter) {
                // No hacemos nada aquí, solo queremos que el usuario vea los filtros
            }
        });
        
        // Mostrar filtros flotantes
        gridOptions.defaultColDef.floatingFilter = !gridOptions.defaultColDef.floatingFilter;
        gridApi.refreshHeader();
        document.getElementById('icono-filtro').classList.add('active');
    }
}

// Guardar todos los cambios pendientes
function saveAllChanges() {
    if (Object.keys(changedData).length === 0) {
        Swal.fire('Información', 'No hay cambios pendientes por guardar', 'info');
        return;
    }
    
    Swal.fire({
        title: 'Guardando cambios',
        html: 'Por favor espere mientras se guardan los cambios',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            
            // Enviar cambios al servidor
            $.ajax({
                url: './functions/coord-personal-plantilla/guardar-cambios.php',
                method: 'POST',
                data: {
                    changes: JSON.stringify(changedData)
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Limpiar cambios pendientes
                        changedData = {};
                        document.getElementById('icono-guardar').classList.remove('has-changes');
                        
                        Swal.fire('Éxito', 'Cambios guardados correctamente', 'success');
                    } else {
                        Swal.fire('Error', response.message || 'Hubo un error al guardar los cambios', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error de conexión al servidor', 'error');
                }
            });
        }
    });
}

// Deshacer todos los cambios pendientes
function undoAllChanges() {
    if (Object.keys(changedData).length === 0) {
        Swal.fire('Información', 'No hay cambios pendientes por deshacer', 'info');
        return;
    }
    
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Se descartarán todos los cambios pendientes',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, deshacer cambios',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Recargar datos originales
            location.reload();
        }
    });
}

// Eliminar registros seleccionados
function eliminarRegistrosSeleccionados() {
    const selectedNodes = gridApi.getSelectedNodes();
    const selectedIds = selectedNodes.map(node => node.data.ID);
    
    if (selectedIds.length === 0) {
        Swal.fire('Error', 'Por favor seleccione al menos un registro', 'warning');
        return;
    }
    
    Swal.fire({
        title: '¿Está seguro?',
        text: `Se eliminarán ${selectedIds.length} registros`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loader
            Swal.fire({
                title: 'Eliminando registros',
                html: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Llamar a tu función PHP para eliminar
                    $.ajax({
                        url: './functions/coord-personal-plantilla/eliminar-registros.php',
                        method: 'POST',
                        data: {
                            ids: selectedIds
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Eliminar filas de la grid
                                gridApi.applyTransaction({
                                    remove: selectedNodes.map(node => node.data)
                                });
                                Swal.fire('Éxito', 'Registros eliminados correctamente', 'success');
                            } else {
                                Swal.fire('Error', response.message || 'Hubo un error al eliminar los registros', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Error de conexión al servidor', 'error');
                        }
                    });
                }
            });
        }
    });
}

// Exportar a Excel
function exportToExcel() {
    // Configuración de la exportación
    const exportParams = {
        fileName: 'Plantilla_Academica_' + new Date().toISOString().split('T')[0],
        sheetName: 'Datos',
        exportMode: 'xlsx'
    };
    
    // Exportar
    gridApi.exportDataAsExcel(exportParams);
}

// Script para cambiar el encabezado por responsividad
window.addEventListener("resize", function() {
    var tituloContainer = document.querySelector(".encabezado-centro");
    if (window.innerWidth <= 768) {
        tituloContainer.innerHTML = "<h3>Plantilla Académica (C.P)</h3>";
    } else {
        tituloContainer.innerHTML = "<h3>Plantilla Académica - Coordinación de Personal</h3>";
    }
});
</script>

<?php include("./template/footer.php"); ?>