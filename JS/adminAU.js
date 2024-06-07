document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".btn.edit");

  const saveButtons = document.querySelectorAll(".btn.save");

  const cancelButtons = document.querySelectorAll(".btn.cancel");

  editButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      const row = button.closest("tr");

      toggleEdit(row, true);
    });
  });

  saveButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      const row = button.closest("tr");

      saveChanges(row);
    });
  });

  cancelButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      const row = button.closest("tr");

      toggleEdit(row, false);
    });
  });

  function toggleEdit(row, isEditing) {
    const editableFields = row.querySelectorAll(".editable");

    editableFields.forEach((field) => {
      const fieldName = field.getAttribute("data-field");

      if (isEditing) {
        const value = field.innerText;

        if (fieldName === "Rol") {
          field.innerHTML = getRolesDropdown(value);
        } else if (fieldName === "Departamento") {
          field.innerHTML = getDepartamentosDropdown(value);
        } else {
          field.innerHTML = `<input type='text' value='${value}' data-field='${fieldName}'>`;
        }
      } else {
        const input = field.querySelector("input");

        const select = field.querySelector("select");

        //reloadPage();

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

    row.querySelector(".delete").style.display = isEditing ? "none" : ""; // Ocultar botón de borrar al editar
  }

  function getRolesDropdown(currentRole) {
    let dropdown = `<select data-field='Rol'>`;

    roles.forEach((role) => {
      dropdown += `<option value='${role.Rol_ID}' ${
        role.Nombre_Rol === currentRole ? "selected" : ""
      }>${role.Nombre_Rol}</option>`;
    });

    dropdown += `</select>`;

    return dropdown;
  }

  function getDepartamentosDropdown(currentDepartamento) {
    let dropdown = `<select data-field='Departamento'>`;

    departamentos.forEach((departamento) => {
      dropdown += `<option value='${departamento.Departamento_ID}' ${
        departamento.Nombre_Departamento === currentDepartamento
          ? "selected"
          : ""
      }>${departamento.Nombre_Departamento}</option>`;
    });

    dropdown += `</select>`;

    return dropdown;
  }

  function saveChanges(row) {
    const userId = row.getAttribute("data-id");

    const inputs = row.querySelectorAll("input");

    const selects = row.querySelectorAll("select");

    const data = {};

    inputs.forEach((input) => {
      const fieldName = input.getAttribute("data-field");

      data[fieldName] = input.value;
    });

    selects.forEach((select) => {
      const fieldName = select.getAttribute("data-field");

      data[fieldName] = select.value;
    });

    console.log("Sending data to server:", { id: userId, ...data }); // Log data está siendo enviada

    fetch("config/editarUsuario.php", {
      method: "POST",

      headers: {
        "Content-Type": "application/json",
      },

      body: JSON.stringify({
        id: userId,

        ...data,
      }),
    })
      .then((response) => response.json())

      .then((result) => {
        console.log("Server response:", result); // Log repuesta del servidor

        if (result.success) {
          inputs.forEach((input) => {
            const fieldName = input.getAttribute("data-field");

            row.querySelector(`.editable[data-field=${fieldName}]`).innerText =
              input.value;

            //reloadPage();
          });

          selects.forEach((select) => {
            const fieldName = select.getAttribute("data-field");

            row.querySelector(`.editable[data-field=${fieldName}]`).innerText =
              select.options[select.selectedIndex].text;
          });

          toggleEdit(row, false);
        } else {
          console.error(result.message); //Mensaje para hacer debug

          alert("Error al actualizar el usuario: " + result.message); // Mostrar mensaje de error
        }
      })

      .catch((error) => {
        console.error("Error:", error);

        alert("Error al actualizar el usuario.");
      });
  }

  // Eliminar Usuario
  const deleteButtons = document.querySelectorAll(".btn.delete");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      const row = button.closest("tr");
      const userId = row.getAttribute("data-id");

      if (
        confirm(`¿Estás seguro de eliminar al usuario con código ${userId}?`)
      ) {
        fetch("config/eliminarUsuario.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ id: userId }),
        })
          .then((response) => response.json())
          .then((result) => {
            console.log("Server response:", result);
            if (result.success) {
              row.remove();
              alert("Usuario eliminado exitosamente");
            } else {
              alert("Error al eliminar el usuario: " + result.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Error al eliminar el usuario.");
          });
      }
    });
  });

  // Barra de búsqueda
  const searchInput = document.getElementById("search-input");

  searchInput.addEventListener("input", filterTable);

  function filterTable() {
    const searchText = searchInput.value.toLowerCase().trim();

    const searchWords = searchText.split(" ");

    const tableRows = document.querySelectorAll("table tr:not(:first-child)");
    tableRows.forEach((row) => {
      const rowData = row.textContent.toLowerCase();
      const shouldShowRow = searchWords.every((word) => rowData.includes(word));
      if (shouldShowRow) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  }
});

filterTable();

//function reloadPage() {
//window.location.reload();
//}
