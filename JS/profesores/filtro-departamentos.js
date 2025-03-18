document.addEventListener('DOMContentLoaded', function() {
    // Get the table body element
    const tableBody = document.querySelector('.profesores-table tbody');
    const originalRows = Array.from(tableBody.querySelectorAll('tr'));
    
    // Add "Seleccionar Todos" checkbox
    const dropdownList = document.querySelector('.items');
    const selectAllLi = document.createElement('li');
    selectAllLi.innerHTML = '<input type="checkbox" id="select-all"/>Todos los departamentos';
    dropdownList.insertBefore(selectAllLi, dropdownList.firstChild);
    
    // Get all department checkboxes
    const departmentCheckboxes = dropdownList.querySelectorAll('input[type="checkbox"]:not(#select-all)');
    const selectAllCheckbox = document.getElementById('select-all');
    const dropdownAnchor = document.querySelector('.anchor');

    // Variable para mantener el estado previo de las selecciones
    let previousSelections = new Set();

    function updateSelectionCount() {
        const checkedDepartments = Array.from(departmentCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.parentElement.textContent.trim());
        const checkedCount = checkedDepartments.length;
        if (selectAllCheckbox.checked) {
            dropdownAnchor.textContent = 'Todos los departamentos';
        } else if (checkedCount === 0) {
            dropdownAnchor.textContent = 'Departamentos ';
        } else if (checkedCount === 1) {
            dropdownAnchor.innerHTML = `Departamento: ${checkedDepartments[0]}`;
        } else {
            dropdownAnchor.textContent = `${checkedCount} departamentos seleccionados`;
        }
    }
    
    // Actualiza la tabla con base en los departamentos seleccionados
    function updateTable() {
        const selectedDepartments = Array.from(departmentCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.parentElement.textContent.trim());
        
        tableBody.innerHTML = '';
        
        // Verificar si hay una fila de "no hay datos" en los datos originales
        const noDataRow = originalRows.find(row => row.id === 'no-data-row');

        // Si hay una fila de "no hay datos", simplemente mostrarla y salir
        if (noDataRow) {
            tableBody.appendChild(noDataRow.cloneNode(true));
            updateSelectionCount();
            ajustarColumnas();
            return;
        }

        if (selectAllCheckbox.checked || selectedDepartments.length > 0) {
            const departmentsToShow = selectAllCheckbox.checked ? [] : selectedDepartments;
            
            let rowsAdded = 0;
            originalRows.forEach(row => {
                const departmentCell = row.querySelector('td:nth-child(4)');
                const departmentValue = departmentCell ? departmentCell.textContent.trim() : '';
                
                // Mostrar todas las filas si "Todos los departamentos" está seleccionado
                // o solo las filas de los departamentos seleccionados
                if (selectAllCheckbox.checked || 
                    departmentsToShow.some(dept => 
                        departmentValue.toLowerCase() === dept.toLowerCase())) {
                    const newRow = row.cloneNode(true);
                    newRow.style.display = '';
                    tableBody.appendChild(newRow);
                    rowsAdded++;
                }
            });
            
            // Si no se agregaron filas después del filtrado, mostrar mensaje de no hay datos
            if (rowsAdded === 0) {
                const noResultsRow = document.createElement('tr');
                noResultsRow.innerHTML = '<td colspan="8">No hay información disponible para los filtros seleccionados</td>';
                tableBody.appendChild(noResultsRow);
            }
        } else {
            // Si no hay departamentos seleccionados, mostrar mensaje de seleccionar departamentos
            const noSelectionRow = document.createElement('tr');
            noSelectionRow.innerHTML = '<td colspan="8">Seleccione al menos un departamento para ver la información</td>';
            tableBody.appendChild(noSelectionRow);
        }
        
        updateSelectionCount();
        ajustarColumnas();
    }
    
    // Manejador del checkbox "Seleccionar Todo"
    selectAllCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Guardar selecciones previas antes de seleccionar todos
            previousSelections = new Set(
                Array.from(departmentCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.parentElement.textContent.trim())
            );
            
            departmentCheckboxes.forEach(cb => {
                cb.checked = true;
            });
        } else {
            // Restaurar selecciones previas
            departmentCheckboxes.forEach(cb => {
                cb.checked = previousSelections.has(cb.parentElement.textContent.trim());
            });
        }
        updateTable();
    });
    
    // Manejador del checkbox de departamentos individuales
    departmentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = Array.from(departmentCheckboxes).filter(cb => cb.checked).length;
            
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else if (checkedCount === departmentCheckboxes.length) {
                selectAllCheckbox.checked = true;
            }
            
            updateTable();
        });
    });
    
    // Manejador inicial de selección de departamento
    if (typeof sessionDepartment !== 'undefined' && sessionDepartment) {
        if (isPosgrados === 'true') {
            selectAllCheckbox.checked = true;
            departmentCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
                previousSelections.add(checkbox.parentElement.textContent.trim());
            });
        } else {
            departmentCheckboxes.forEach(checkbox => {
                const checkboxDepartment = checkbox.parentElement.textContent.trim();
                if (checkboxDepartment.toLowerCase() === sessionDepartment.toLowerCase()) {
                    checkbox.checked = true;
                    previousSelections.add(checkboxDepartment);
                }
            });
        }
        updateTable();
    } else {
        updateTable();
    }
});