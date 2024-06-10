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

    // Enviar los datos al servidor usando AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "./config/guardarUsuario.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        // Manejar la respuesta del servidor
        console.log(xhr.responseText);
        // Aquí puedes agregar código para actualizar la tabla de usuarios u otras acciones
        ocultarModal(); // Ocultar el modal después de guardar el usuario
      }
    };
    xhr.send(JSON.stringify(datos));
  };

  // Evento click para cerrar el modal al hacer clic fuera de él
  window.onclick = function (event) {
    if (event.target === nuevoUsuarioModal) {
      ocultarModal();
    }
  };
});

filterTable();

//function reloadPage() {
//window.location.reload();
//}
