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
    
    // Function to update table based on selected departments
    function updateTable() {
        const selectedDepartments = Array.from(departmentCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.parentElement.textContent.trim());
        
        // Clear the table first
        tableBody.innerHTML = '';
        
        // If "Select All" is checked, show all rows
        if (selectAllCheckbox.checked) {
            let counter = 1;
            originalRows.forEach(row => {
                const newRow = row.cloneNode(true);
                newRow.querySelector('td:first-child').textContent = counter++;
                tableBody.appendChild(newRow);
            });
            return;
        }
        
        // If no departments selected and "Select All" is not checked, show nothing
        if (selectedDepartments.length === 0) {
            return;
        }
        
        // Show rows for selected departments
        let counter = 1;
        originalRows.forEach(row => {
            const departmentCell = row.querySelector('td:nth-child(5)');
            const departmentValue = departmentCell ? departmentCell.textContent.trim() : '';
            
            // Check if the department matches any selected department
            if (selectedDepartments.some(dept => 
                departmentValue.toLowerCase() === dept.toLowerCase())) {
                const newRow = row.cloneNode(true);
                newRow.querySelector('td:first-child').textContent = counter++;
                newRow.style.display = ''; // Ensure the row is visible
                tableBody.appendChild(newRow);
            }
        });
    }
    
    // Handle "Select All" checkbox
    selectAllCheckbox.addEventListener('change', function() {
        departmentCheckboxes.forEach(cb => {
            cb.checked = this.checked;
        });
        updateTable();
    });
    
    // Handle individual department checkboxes
    departmentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // If unchecking and it's the last checkbox, uncheck "Select All"
            if (!this.checked && Array.from(departmentCheckboxes).filter(cb => cb.checked).length === 0) {
                selectAllCheckbox.checked = false;
            }
            
            // If all individual checkboxes are checked, check "Select All"
            selectAllCheckbox.checked = Array.from(departmentCheckboxes)
                .every(cb => cb.checked);
            
            updateTable();
        });
    });
    
    // Initialize table with no departments selected
    updateTable();
});