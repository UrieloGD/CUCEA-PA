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
    
    // Function to update table based on selected departments
    function updateTable() {
        const selectedDepartments = Array.from(departmentCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.parentElement.textContent.trim());
        
        tableBody.innerHTML = '';

        // Si "Todos los departamentos" está seleccionado o si hay departamentos individuales seleccionados
        if (selectAllCheckbox.checked || selectedDepartments.length > 0) {
            const departmentsToShow = selectAllCheckbox.checked ? [] : selectedDepartments;
            
            originalRows.forEach(row => {
                const departmentCell = row.querySelector('td:nth-child(4)'); // Ajustado el índice por la eliminación de la columna count
                const departmentValue = departmentCell ? departmentCell.textContent.trim() : '';
                
                // Mostrar todas las filas si "Todos los departamentos" está seleccionado
                // o solo las filas de los departamentos seleccionados
                if (selectAllCheckbox.checked || 
                    departmentsToShow.some(dept => 
                        departmentValue.toLowerCase() === dept.toLowerCase())) {
                    const newRow = row.cloneNode(true);
                    newRow.style.display = '';
                    tableBody.appendChild(newRow);
                }
            });
        }
        updateSelectionCount();
    }
    
    // Handle "Select All" checkbox
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
    
    // Handle individual department checkboxes
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
    
    // Handle initial department selection
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