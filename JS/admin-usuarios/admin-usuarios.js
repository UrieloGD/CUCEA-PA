// Interacción con botones de edición, guardado, cancelación, y la apertura/cierre
// de un modal para agregar nuevos usuarios.
document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".btn.edit");
  const saveButtons = document.querySelectorAll(".btn.save");
  const cancelButtons = document.querySelectorAll(".btn.cancel");

  const addButton = document.getElementById("add-button");
  const nuevoUsuarioModal = document.getElementById("nuevoUsuarioModal");
  const cerrarModal = document.getElementsByClassName("cerrar")[0];
  const nuevoUsuarioForm = document.getElementById("nuevoUsuarioForm");
  const submitButton = document.getElementById("submitButton");

  // Funciones de modal
  function mostrarModal() {
    nuevoUsuarioModal.style.display = "block";
  }

  function ocultarModal() {
    nuevoUsuarioModal.style.display = "none";
    resetModalForm();
  }

  function resetModalForm() {
    nuevoUsuarioModal.setAttribute("data-mode", "add");
    document.getElementById("modalTitle").textContent = "Agregar nuevo usuario";
    document.getElementById("usuarioIdEdicion").value = "";

    document.getElementById("codigo").removeAttribute("readonly"); // Desbloquea código
    document.getElementById("codigo").value = ""; // Limpia código

    document.getElementById("nombre").value = "";
    document.getElementById("apellido").value = "";
    document.getElementById("correo").value = "";
    document.getElementById("rol").selectedIndex = 0;
    document.getElementById("departamento").value = ""; // Selecciona la opción vacía
    document.getElementById("password").removeAttribute("readonly"); // Desbloquea contraseña
    document.getElementById("password").value = ""; // Limpia contraseña
    submitButton.textContent = "Guardar";
  }

  // Lógica de roles y departamentos
  document.getElementById("rol").addEventListener("change", function () {
    const selectedRole = this.options[this.selectedIndex].text;
    const departamentosSelect = document.getElementById("departamento");
    const emptyOption = departamentosSelect.querySelector('option[value=""]');

    if (
      selectedRole === "Secretaría Administrativa" ||
      selectedRole === "Coordinación de Personal" ||
      selectedRole === "Administrador"
    ) {
      departamentosSelect.disabled = true;
      departamentosSelect.selectedIndex = ""; // Selecciona la opción vacía
      emptyOption.style.display = ""; // Muestra la opción vacía
    } else {
      departamentosSelect.disabled = false;
      emptyOption.style.display = "none"; // Oculta la opción vacía
      if (departamentosSelect.value === "") {
        departamentosSelect.selectedIndex = 1;
      }
    }
  });

  // Cargar datos de usuario para edición
  function cargarDatosUsuarioEnModal(usuario) {
    nuevoUsuarioModal.setAttribute("data-mode", "edit");
    document.getElementById("modalTitle").textContent = "Editar Usuario";
    document.getElementById("usuarioIdEdicion").value = usuario.Codigo;

    document.getElementById("codigo").value = usuario.Codigo;
    // document.getElementById('codigo').setAttribute('readonly', true);

    document.getElementById("nombre").value = usuario.Nombre;
    document.getElementById("apellido").value = usuario.Apellido;
    document.getElementById("correo").value = usuario.Correo;

    // Establecer rol
    const rolSelect = document.getElementById("rol");
    for (let i = 0; i < rolSelect.options.length; i++) {
      if (rolSelect.options[i].text === usuario.Nombre_Rol) {
        rolSelect.selectedIndex = i;
        break;
      }
    }

    // Manejar el departamento basado en el rol
    const departamentoSelect = document.getElementById("departamento");
    if (
      usuario.Nombre_Rol === "Secretaría Administrativa" ||
      usuario.Nombre_Rol === "Coordinación de Personal" ||
      usuario.Nombre_Rol === "Administrador"
    ) {
      // Deshabilitar y limpiar el departamento
      departamentoSelect.disabled = true;
      departamentoSelect.selectedIndex = ""; // Selecciona la opció vacía
    } else {
      // Habilitar y establecer el departamento
      departamentoSelect.disabled = false;
      emptyOption.style.display = "none"; // Oculta la opción vacía
      for (let i = 0; i < departamentoSelect.options.length; i++) {
        if (departamentoSelect.options[i].text === usuario.departamento) {
          departamentoSelect.selectedIndex = i;
          break;
        }
      }
    }

    // Establecer género
    const generoSelect = document.getElementById("genero");
    for (let i = 0; i < generoSelect.options.length; i++) {
      if (generoSelect.options[i].value === usuario.Genero) {
        generoSelect.selectedIndex = i;
        break;
      }
    }

    // Deshabilitar contraseña
    document.getElementById("password").setAttribute("readonly", true);
    document.getElementById("password").value = "********";

    submitButton.textContent = "Actualizar";
    mostrarModal();
  }

  // Evento de botones de edición
  editButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      const row = button.closest("tr");
      const userData = {
        Codigo:
          row.querySelector('[data-field="Código"]')?.textContent ||
          row.querySelector("td:first-child").textContent,
        Nombre: row.querySelectorAll("td")[1].textContent,
        Apellido: row.querySelectorAll("td")[2].textContent,
        Correo: row.querySelectorAll("td")[3].textContent,
        Nombre_Rol: row.querySelectorAll("td")[4].textContent,
        departamento: row.querySelectorAll("td")[5].textContent,
        Genero: row.querySelector("[data-genero]")?.getAttribute("data-genero"),
      };

      cargarDatosUsuarioEnModal(userData);
    });
  });

  // Eventos del modal
  addButton.onclick = mostrarModal;
  cerrarModal.onclick = ocultarModal;
  window.onclick = function (event) {
    if (event.target === nuevoUsuarioModal) {
      ocultarModal();
    }
  };

  // Envío del formulario
  nuevoUsuarioForm.onsubmit = function (event) {
    event.preventDefault();

    const mode = nuevoUsuarioModal.getAttribute("data-mode");

    if (mode === "edit") {
      // Lógica de edición
      const datos = {
        id: document.getElementById("usuarioIdEdicion").value,
        Codigo: document.getElementById("codigo").value, // Añadir esta línea
        Nombre: document.getElementById("nombre").value,
        Apellido: document.getElementById("apellido").value,
        Correo: document.getElementById("correo").value,
        Rol: document.getElementById("rol").value,
        Departamento: document.getElementById("departamento").value,
        Genero: document.getElementById("genero").value,
      };

      fetch("./functions/admin-usuarios/editarUsuario.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(datos),
      })
        .then((response) => response.json())
        .then((result) => {
          ocultarModal();

          if (result.success) {
            Swal.fire({
              icon: "success",
              title: "¡Éxito!",
              text: result.message,
              customClass: {
                popup: "high-z-index",
                confirmButton: "OK-boton",
              },
            }).then(() => {
              reloadPage();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: result.message,
              customClass: {
                popup: "high-z-index",
                confirmButton: "OK-boton",
              },
            });
          }
        });
    } else {
      // Lógica de creación de usuario
      const datos = {
        codigo: document.getElementById("codigo").value,
        nombre: document.getElementById("nombre").value,
        apellido: document.getElementById("apellido").value,
        correo: document.getElementById("correo").value,
        rol: document.getElementById("rol").value,
        departamento: document.getElementById("departamento").value,
        genero: document.getElementById("genero").value,
        password: document.getElementById("password").value,
      };

      fetch("./functions/admin-usuarios/agregarUsuario.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(datos),
      })
        .then((response) => response.json())
        .then((result) => {
          ocultarModal();

          if (result.success) {
            Swal.fire({
              icon: "success",
              title: "Éxito",
              text: result.message,
              customClass: {
                popup: "high-z-index",
                confirmButton: "OK-boton",
              },
            }).then(() => {
              reloadPage();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: result.message,
              customClass: {
                popup: "high-z-index",
                confirmButton: "OK-boton",
              },
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un problema al procesar la solicitud: " + error.message,
            customClass: {
              popup: "high-z-index",
              confirmButton: "OK-boton",
            },
          });
        });
    }
  };

  function reloadPage() {
    window.location.reload();
  }
});
