<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verify user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include header and navbar
include './template/header.php';
include './template/navbar.php';

// Get department ID based on user role
$rol = $_SESSION['Rol_ID'];
$usuario_id = $_SESSION['Codigo'] ?? 0;

if ($rol == 0) { // Admin
    $departamento_id = isset($_GET['Departamento_ID']) ? (int)$_GET['Departamento_ID'] : 1;
} elseif ($rol == 1 || $rol == 4) { // Department heads
    $departamento_id = $_SESSION['Departamento_ID'];
} elseif ($rol == 2 || $rol == 3) { // Other roles
    if (isset($_GET['Departamento_ID'])) {
        $departamento_id = (int)$_GET['Departamento_ID'];
    } else {
        // Select first department if none specified
        $sql_primer_departamento = "SELECT Departamento_ID FROM departamentos ORDER BY Departamento_ID LIMIT 1";
        $result_primer_departamento = mysqli_query($conexion, $sql_primer_departamento);

        if ($result_primer_departamento && mysqli_num_rows($result_primer_departamento) > 0) {
            $row_primer_departamento = mysqli_fetch_assoc($result_primer_departamento);
            $departamento_id = $row_primer_departamento['Departamento_ID'];
        } else {
            die("No se encontraron departamentos disponibles.");
        }
    }
} else {
    die("Rol de usuario no autorizado.");
}

// Get department information
$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();

if (!$result_departamento || $result_departamento->num_rows == 0) {
    die("No se encontró información del departamento.");
}

$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Nombre_Departamento'];
$departamento_nombre = $row_departamento['Departamentos'];

// Prepare table name for the department
$tabla_departamento = "data_" . $nombre_departamento;

// Get data from the department's table
$sql = "SELECT * FROM $tabla_departamento WHERE Departamento_ID = ? AND PAPELERA = 'activo'";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la preparación de la consulta de datos: " . $conexion->error);
}

$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result = $stmt->get_result();

// Convert data to JSON format for Tabulator
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Close the database connection
mysqli_close($conexion);
?>

<title>Data - <?php echo $departamento_nombre; ?></title>

<!-- Include Tabulator CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/tabulator-tables@5.5.2/dist/css/tabulator.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator_bulma.min.css" rel="stylesheet">


<!-- Custom CSS for the table -->
<link rel="stylesheet" href="./CSS/basesdedatos/basesdedatos.css?v=<?php echo filemtime('./CSS/basesdedatos/basesdedatos.css'); ?>" />

<style>
</style>

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="encabezado-izquierda" style="display: flex; align-items: center;">
            <!-- <div class="barra-buscador" id="barra-buscador">
                <div class="icono-buscador" id="icono-buscador">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
                <input type="text" id="input-buscador" placeholder="Buscar...">
            </div> -->
        </div>
        <div class="encabezado-centro">
            <h3>Data - <?php echo $departamento_nombre; ?></h3>
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
                <div class="icono-buscador" id="icono-visibilidad" data-tooltip="Mostrar/ocultar columnas">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-filtro" data-tooltip="Mostrar/ocultar filtros">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()" data-tooltip="Añadir nuevo registro">
                    <i class="fa fa-add" aria-hidden="true"></i>
                </div>
                <!-- <div class="icono-buscador" id="icono-editar" onclick="editarRegistrosSeleccionados()">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </div> -->
                <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()" data-tooltip="Eliminar registros seleccionados">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-descargar" onclick="mostrarDescargarExcel()" data-tooltip="Descargar Excel">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="departamento_id" value="<?php echo $departamento_id; ?>">
    <div id="tabla-datos"></div>
</div>

<script src="./JS/basesdedatos/tabulator-edit-manager.js?v=<?php echo time(); ?>"></script>
<!-- Crear input hidden para el user role si no existe -->
<input type="hidden" id="user-role" value="<?php echo $rol; ?>">

<script>
    //Create Date Editor
    var dateEditor = function(cell, onRendered, success, cancel) {
        //cell - the cell component for the editable cell
        //onRendered - function to call when the editor has been rendered
        //success - function to call to pass thesuccessfully updated value to Tabulator
        //cancel - function to call to abort the edit and return to a normal cell

        //create and style input
        var cellValue = luxon.DateTime.fromFormat(cell.getValue(), "dd/MM/yyyy").toFormat("yyyy-MM-dd"),
            input = document.createElement("input");

        input.setAttribute("type", "date");

        input.style.padding = "4px";
        input.style.width = "100%";
        input.style.boxSizing = "border-box";

        input.value = cellValue;

        onRendered(function() {
            input.focus();
            input.style.height = "100%";
        });

        function onChange() {
            if (input.value != cellValue) {
                success(luxon.DateTime.fromFormat(input.value, "yyyy-MM-dd").toFormat("dd/MM/yyyy"));
            } else {
                cancel();
            }
        }

        //submit new value on blur or change
        input.addEventListener("blur", onChange);

        //submit new value on enter
        input.addEventListener("keydown", function(e) {
            if (e.keyCode == 13) {
                onChange();
            }

            if (e.keyCode == 27) {
                cancel();
            }
        });

        return input;
    };

    // Definir las columnas de tabulator
    const columns = [{
            // Columna de selección de filas
            title: "",
            field: "checkbox",
            formatter: "rowSelection",
            titleFormatter: "rowSelection",
            hozAlign: "center",
            headerSort: false,
            width: 50,
            frozen: true, // Fijar la columna de selección
            editor: false, // Explícitamente no editable
        },

        {
            title: "ID",
            field: "ID_Plantilla",
            sorter: "number",
            //headerFilter: true,
            width: 80,
            frozen: true
        },
        {
            title: "CICLO",
            field: "CICLO",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "CRN",
            field: "CRN",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "MATERIA",
            field: "MATERIA",
            sorter: "string",
            //headerFilter: true,
            width: 250,
            editor: "input"
        },
        {
            title: "CVE MATERIA",
            field: "CVE_MATERIA",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "SECCIÓN",
            field: "SECCION",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "NIVEL",
            field: "NIVEL",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "NIVEL TIPO",
            field: "NIVEL_TIPO",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "TIPO",
            field: "TIPO",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "C. MIN",
            field: "C_MIN",
            sorter: "string",
            //headerFilter: true
            editor: "input",
            validator: ["minLength:1", "maxLength:5", "integer"]
        },
        {
            title: "H. TOTALES",
            field: "H_TOTALES",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "STATUS",
            field: "ESTATUS",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "TIPO CONTRATO",
            field: "TIPO_CONTRATO",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "CÓDIGO",
            field: "CODIGO_PROFESOR",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "NOMBRE PROFESOR",
            field: "NOMBRE_PROFESOR",
            sorter: "string",
            //headerFilter: true,
            width: 200,
            editor: "input"
        },
        {
            title: "CATEGORIA",
            field: "CATEGORIA",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "DESCARGA",
            field: "DESCARGA",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "CÓDIGO DESCARGA",
            field: "CODIGO_DESCARGA",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "NOMBRE DESCARGA",
            field: "NOMBRE_DESCARGA",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "NOMBRE DEFINITIVO",
            field: "NOMBRE_DEFINITIVO",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "TITULAR",
            field: "TITULAR",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "HORAS",
            field: "HORAS",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "CÓDIGO DEPENDENCIA",
            field: "CODIGO_DEPENDENCIA",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "L",
            field: "L",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "M",
            field: "M",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "I",
            field: "I",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "J",
            field: "J",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "V",
            field: "V",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "S",
            field: "S",
            sorter: "string",
            //headerFilter: true
            editor: "input"
        },
        {
            title: "D",
            field: "D",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "DÍA PRESENCIAL",
            field: "DIA_PRESENCIAL",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "DÍA VIRTUAL",
            field: "DIA_VIRTUAL",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "MODALIDAD",
            field: "MODALIDAD",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "FECHA INICIAL",
            field: "FECHA_INICIAL",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "FECHA FINAL",
            field: "FECHA_FINAL",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "HORA INICIAL",
            field: "HORA_INICIAL",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "HORA FINAL",
            field: "HORA_FINAL",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "MÓDULO",
            field: "MODULO",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "AULA",
            field: "AULA",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "CUPO",
            field: "CUPO",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "OBSERVACIONES",
            field: "OBSERVACIONES",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
        {
            title: "EXTRAORDINARIO",
            field: "EXAMEN_EXTRAORDINARIO",
            sorter: "string",
            // headerFilter: true
            editor: "input"
        },
    ];

    // Initialize Tabulator with the dataset from PHP
    var table = new Tabulator("#tabla-datos", {
        data: <?php echo json_encode($data); ?>,
        columns: columns,
        layout: "fitDataFill",
        pagination: "local",
        paginationSize: 25,
        paginationSizeSelector: [15, 25, 50, 100],
        movableColumns: true,
        resizableColumns: true,
        selectable: true,
        selectableRangeMode: "click",
        height: "700px",
        placeholder: "No hay datos disponibles",
        printAsHtml: true,
        printStyled: true,
        headerFilterLiveFilterDelay: 300,
        // Importante: hacer la tabla editable
        cellEditable: function(cell) {
            // Verificar permisos de edición
            const userRole = document.getElementById("user-role")?.value;
            const puedeEditar = window.puedeEditar !== false;

            // Solo permitir edición a roles autorizados y si puedeEditar es true
            return puedeEditar && (userRole === "0" || userRole === "1" || userRole === "4");
        }
    });

    // Hacer la tabla accesible globalmente
    window.table = table;

    // Export table data to Excel function (mantén tu función actual)
    document.getElementById("icono-descargar").addEventListener("click", function() {
        table.download("xlsx", "data_<?php echo $departamento_nombre; ?>.xlsx", {
            sheetName: "Data Departamento"
        });
    });

    // Toggle header filters (mantén tu función actual)
    document.getElementById("icono-filtro").addEventListener("click", function() {
        table.toggleHeaderFilter();
    });
</script>

<?php include("./template/footer.php"); ?>