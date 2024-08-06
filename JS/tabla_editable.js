function makeEditable() {
    const table = document.getElementById('tabla-datos');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) { // Empezamos desde 1 para saltar la fila de encabezados
        const cells = rows[i].getElementsByTagName('td');
        for (let j = 1; j < cells.length; j++) { // Empezamos desde 1 para saltar la columna ID
            if (j !== 1) { // No hacemos editable la columna ID
                cells[j].setAttribute('contenteditable', 'true');
                cells[j].addEventListener('blur', function() {
                    updateCell(this);
                });
            }
        }
    }
}

// Mapear los nombres de las columnas si es necesario
const columnMap = {
    'ID': 'ID_Plantilla',
    'CICLO': 'CICLO',
    'CRN': 'CRN',
    'MATERIA': 'MATERIA',
    'CVE MATERIA': 'CVE_MATERIA',
    'SECCIÓN': 'SECCION',
    'NIVEL': 'NIVEL',
    'NIVEL TIPO': 'NIVEL_TIPO',
    'TIPO': 'TIPO',
    'C. MIN': 'C_MIN',
    'H. TOTALES': 'H_TOTALES',
    'STATUS': 'ESTATUS',
    'TIPO CONTRATO': 'TIPO_CONTRATO',
    'CÓDIGO': 'CODIGO_PROFESOR',
    'NOMBRE PROFESOR': 'NOMBRE_PROFESOR',
    'CATEGORIA': 'CATEGORIA',
    'DESCARGA': 'DESCARGA',
    'CÓDIGO DESCARGA': 'CODIGO_DESCARGA',
    'NOMBRE DESCARGA': 'NOMBRE_DESCARGA',
    'NOMBRE DEFINITIVO': 'NOMBRE_DEFINITIVO',
    'TITULAR': 'TITULAR',
    'HORAS': 'HORAS',
    'CÓDIGO DEPENDENCIA': 'CODIGO_DEPENDENCIA',
    'L': 'L',
    'M': 'M',
    'I': 'I',
    'J': 'J',
    'V': 'V',
    'S': 'S',
    'D': 'D',
    'DÍA PRESENCIAL': 'DIA_PRESENCIAL',
    'DÍA VIRTUAL': 'DIA_VIRTUAL',
    'MODALIDAD': 'MODALIDAD',
    'FECHA INICIAL': 'FECHA_INICIAL',
    'FECHA FINAL': 'FECHA_FINAL',
    'HORA INICIAL': 'HORA_INICIAL',
    'HORA FINAL': 'HORA_FINAL',
    'MÓDULO': 'MODULO',
    'AULA': 'AULA',
    'CUPO': 'CUPO',
    'OBSERVACIONES': 'OBSERVACIONES',
    'EXTRAORDINARIO': 'EXAMEN_EXTRAORDINARIO'
};

// Función para actualizar una celda
function updateCell(cell) {
    const row = cell.parentNode;
    const id = row.cells[1].textContent; // Asumiendo que el ID está en la segunda columna
    const columnIndex = cell.cellIndex;
    const newValue = cell.textContent;
    
    // Obtener el nombre de la columna del encabezado de la tabla
    const headerRow = document.querySelector('#tabla-datos tr');
    let columnName = headerRow.cells[columnIndex].textContent;

    if (columnMap[columnName]) {
        columnName = columnMap[columnName];
    }

     // Obtener el departamento_id
     const departamentoId = document.getElementById('departamento_id').value;
    
     // Mostrar indicador de carga
     cell.style.backgroundColor = '#FFFACD'; // Amarillo claro para indicar carga
     
     // Enviar la actualización al servidor
     fetch('actualizar_celda.php', {
         method: 'POST',
         headers: {
             'Content-Type': 'application/x-www-form-urlencoded',
         },
         body: `id=${encodeURIComponent(id)}&column=${encodeURIComponent(columnName)}&value=${encodeURIComponent(newValue)}&departamento_id=${encodeURIComponent(departamentoId)}`
     })
     .then(response => response.json())
     .then(data => {
         if (data.success) {
             cell.style.backgroundColor = '#90EE90'; // Verde claro para indicar éxito
             setTimeout(() => {
                 cell.style.backgroundColor = '';
             }, 2000);
         } else {
             cell.style.backgroundColor = '#FFB6C1'; // Rosa claro para indicar error
             cell.textContent = data.oldValue || ''; // Revertir al valor anterior si está disponible
             console.error('Error al actualizar:', data.error);
             alert(`Error al actualizar: ${data.error}`);
         }
     })
     .catch(error => {
         console.error('Error:', error);
         cell.style.backgroundColor = '#FFB6C1'; // Rosa claro para indicar error
         alert(`Error de red o del servidor: ${error.message}`);
     });
 }
 
 // Llamar a makeEditable cuando se carga la página
 document.addEventListener('DOMContentLoaded', makeEditable);