document.addEventListener("DOMContentLoaded", function() {
    const editButtons = document.querySelectorAll(".btn.edit");
    const saveButtons = document.querySelectorAll(".btn.save");
    const cancelButtons = document.querySelectorAll(".btn.cancel");

    editButtons.forEach(button => {
        button.addEventListener("click", function(event) {
            event.preventDefault();
            const row = button.closest("tr");
            toggleEdit(row, true);
        });
    });

    saveButtons.forEach(button => {
        button.addEventListener("click", function(event) {
            event.preventDefault();
            const row = button.closest("tr");
            saveChanges(row);
        });
    });

    cancelButtons.forEach(button => {
        button.addEventListener("click", function(event) {
            event.preventDefault();
            const row = button.closest("tr");
            toggleEdit(row, false);
        });
    });

    function toggleEdit(row, isEditing) {
        const editableFields = row.querySelectorAll(".editable");
        editableFields.forEach(field => {
            const fieldName = field.getAttribute("data-field");
            if (isEditing) {
                const value = field.innerText;
                if (fieldName === 'Rol') {
                    field.innerHTML = getRolesDropdown(value);
                } else if (fieldName === 'Departamento') {
                    field.innerHTML = getDepartamentosDropdown(value);
                } else {
                    field.innerHTML = `<input type='text' value='${value}' data-field='${fieldName}'>`;
                }
            } else {
                const input = field.querySelector("input");
                const select = field.querySelector("select");
                if (input) {
                    field.innerText = input.value;
                } else if (select) {
                    field.innerText = select.options[select.selectedIndex].text;
                }
            }
        });
        row.querySelector(".edit").style.display = isEditing ? "none" : "";
        row.querySelector(".save").style.display = isEditing ? "" : "none";
        row.querySelector(".cancel").style.display = isEditing ? "" : "none";
    }

    function getRolesDropdown(currentRole) {
        let dropdown = `<select data-field='Rol'>`;
        roles.forEach(role => {
            dropdown += `<option value='${role.Rol_ID}' ${role.Nombre_Rol === currentRole ? 'selected' : ''}>${role.Nombre_Rol}</option>`;
        });
        dropdown += `</select>`;
        return dropdown;
    }

    function getDepartamentosDropdown(currentDepartamento) {
        let dropdown = `<select data-field='Departamento'>`;
        departamentos.forEach(departamento => {
            dropdown += `<option value='${departamento.Departamento_ID}' ${departamento.Nombre_Departamento === currentDepartamento ? 'selected' : ''}>${departamento.Nombre_Departamento}</option>`;
        });
        dropdown += `</select>`;
        return dropdown;
    }

    function saveChanges(row) {
        const userId = row.getAttribute("data-id");
        const inputs = row.querySelectorAll("input");
        const selects = row.querySelectorAll("select");
        const data = {};

        inputs.forEach(input => {
            const fieldName = input.getAttribute("data-field");
            data[fieldName] = input.value;
        });

        selects.forEach(select => {
            const fieldName = select.getAttribute("data-field");
            data[fieldName] = select.value;
        });

        console.log("Sending data to server:", { id: userId, ...data }); // Log the data being sent

        fetch("config/editarUsuario.php", {  // Actualiza la ruta aquí si es necesario
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id: userId,
                ...data
            })
        })
        .then(response => response.json())
        .then(result => {
            console.log("Server response:", result); // Log the server response
            if (result.success) {
                inputs.forEach(input => {
                    const fieldName = input.getAttribute("data-field");
                    row.querySelector(`.editable[data-field=${fieldName}]`).innerText = input.value;
                });
                selects.forEach(select => {
                    const fieldName = select.getAttribute("data-field");
                    row.querySelector(`.editable[data-field=${fieldName}]`).innerText = select.options[select.selectedIndex].text;
                });
                toggleEdit(row, false);
            } else {
                console.error(result.message); // Debug message
                alert("Error al actualizar el usuario: " + result.message); // Show error message
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error al actualizar el usuario.");
        });
    }
});