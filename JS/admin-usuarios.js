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
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Se perderán los cambios no guardados.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, cancelar edición",
        cancelButtonText: "No, continuar editando",
      }).then((result) => {
        if (result.isConfirmed) {
          reloadPage(); // recargar página al cancelar edición
        }
      });
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

    console.log("Sending data to server:", { id: userId, ...data });

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
        console.log("Server response:", result);
        if (result.success) {
          Swal.fire({
            icon: "success",
            title: "¡Éxito!",
            text: "Usuario actualizado exitosamente",
          }).then(() => {
            reloadPage();
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al actualizar el usuario: " + result.message,
          });
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un problema al procesar la solicitud.",
        });
      });
  }

  // Eliminar Usuario
  const deleteButtons = document.querySelectorAll(".btn.delete");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      const row = button.closest("tr");
      const userId = row.getAttribute("data-id");

      Swal.fire({
        title: "¿Estás seguro?",
        text: `¿Quieres eliminar al usuario con código ${userId}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
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
                Swal.fire(
                  "Eliminado",
                  "El usuario ha sido eliminado exitosamente.",
                  "success"
                );
              } else {
                Swal.fire(
                  "Error",
                  "Error al eliminar el usuario: " + result.message,
                  "error"
                );
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              Swal.fire("Error", "Error al eliminar el usuario.", "error");
            });
        }
      });
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

  // Obtener referencias a los elementos del DOM
  const addButton = document.getElementById("add-button");
  const nuevoUsuarioModal = document.getElementById("nuevoUsuarioModal");
  const cerrarModal = document.getElementsByClassName("cerrar")[0];
  const nuevoUsuarioForm = document.getElementById("nuevoUsuarioForm");

  // Rellenar las opciones de los select de roles y departamentos
  const rolesSelect = document.getElementById("rol");
  roles.forEach((rol) => {
    const option = document.createElement("option");
    option.value = rol.Rol_ID;
    option.text = rol.Nombre_Rol;
    rolesSelect.add(option);
  });

  const departamentosSelect = document.getElementById("departamento");
  departamentos.forEach((departamento) => {
    const option = document.createElement("option");
    option.value = departamento.Departamento_ID;
    option.text = departamento.Nombre_Departamento;
    departamentosSelect.add(option);
  });

  // Función para mostrar el modal
  function mostrarModal() {
    nuevoUsuarioModal.style.display = "block";
  }

  // Función para ocultar el modal
  function ocultarModal() {
    nuevoUsuarioModal.style.display = "none";
  }

  // Evento click para mostrar el modal al hacer clic en el botón "Añadir usuario"
  addButton.onclick = function () {
    mostrarModal();
  };

  // Evento click para ocultar el modal al hacer clic en la "x"
  cerrarModal.onclick = function () {
    ocultarModal();
  };

  // Evento submit para enviar los datos del formulario
  nuevoUsuarioForm.onsubmit = function (event) {
    event.preventDefault(); // Evitar el envío del formulario por defecto

    // Obtener los valores del formulario
    const codigo = document.getElementById("codigo").value;
    const nombre = document.getElementById("nombre").value;
    const apellido = document.getElementById("apellido").value;
    const correo = document.getElementById("correo").value;
    const rol = document.getElementById("rol").value;
    const departamento = document.getElementById("departamento").value;
    const generoSelect = document.getElementById("genero");
    const genero = generoSelect.options[generoSelect.selectedIndex].value;
    const password = document.getElementById("password").value;

    // Crear un objeto con los datos del formulario
    const datos = {
      codigo,
      nombre,
      apellido,
      correo,
      rol,
      departamento,
      genero,
      password,
    };

    // Enviar los datos al servidor usando fetch
    fetch("./config/guardarUsuario.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(datos),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          Swal.fire({
            icon: "success",
            title: "¡Éxito!",
            text: result.message,
          }).then(() => {
            ocultarModal(); // Ocultar el modal después de guardar el usuario
            // Aquí puedes agregar código para actualizar la tabla de usuarios si es necesario
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message,
          });
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un problema al procesar la solicitud.",
        });
      });
  };

  // Evento click para cerrar el modal al hacer clic fuera de él
  window.onclick = function (event) {
    if (event.target === nuevoUsuarioModal) {
      ocultarModal();
    }
  };
});

function reloadPage() {
  window.location.reload();
}
