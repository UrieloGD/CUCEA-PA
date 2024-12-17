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
    nuevoUsuarioModal.setAttribute('data-mode', 'add');
    document.getElementById('modalTitle').textContent = 'Agregar nuevo usuario';
    document.getElementById('usuarioIdEdicion').value = '';
    document.getElementById('codigo').value = '';
    document.getElementById('nombre').value = '';
    document.getElementById('apellido').value = '';
    document.getElementById('correo').value = '';
    document.getElementById('rol').selectedIndex = 0;
    document.getElementById('departamento').selectedIndex = 0;
    document.getElementById('password').removeAttribute('readonly'); // Desbloquea contraseña
    document.getElementById('password').value = ''; // Limpia contraseña
    submitButton.textContent = 'Guardar';
}

  // Rellenar select de roles y departamentos
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
    option.text = departamento.Departamentos;
    departamentosSelect.add(option);
  });

  // Lógica de roles y departamentos
  rolesSelect.addEventListener("change", function () {
    const selectedRole = this.options[this.selectedIndex].text;
    const departamentosSelect = document.getElementById("departamento");
    
    if (
      selectedRole === "Secretaría Administrativa" ||
      selectedRole === "Coordinación de Personal"
    ) {
      departamentosSelect.disabled = true;
      departamentosSelect.value = "";
    } else {
      departamentosSelect.disabled = false;
    }
  });

  // Cargar datos de usuario para edición
  function cargarDatosUsuarioEnModal(usuario) {
    nuevoUsuarioModal.setAttribute('data-mode', 'edit');
    document.getElementById('modalTitle').textContent = 'Editar Usuario';
    document.getElementById('usuarioIdEdicion').value = usuario.Codigo;
    
    document.getElementById('codigo').value = usuario.Codigo;
    document.getElementById('codigo').setAttribute('readonly', true);
    
    document.getElementById('nombre').value = usuario.Nombre;
    document.getElementById('apellido').value = usuario.Apellido;
    document.getElementById('correo').value = usuario.Correo;
    
    // Establecer rol
    const rolSelect = document.getElementById('rol');
    for (let i = 0; i < rolSelect.options.length; i++) {
      if (rolSelect.options[i].text === usuario.Nombre_Rol) {
        rolSelect.selectedIndex = i;
        break;
      }
    }
    
    // Establecer departamento
    const departamentoSelect = document.getElementById('departamento');
    for (let i = 0; i < departamentoSelect.options.length; i++) {
      if (departamentoSelect.options[i].text === usuario.departamento) {
        departamentoSelect.selectedIndex = i;
        break;
      }
    }
    
    // Deshabilitar contraseña
    document.getElementById('password').setAttribute('readonly', true);
    document.getElementById('password').value = '********';
    
    submitButton.textContent = 'Actualizar';
    mostrarModal();
  }

  // Evento de botones de edición
  editButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      const row = button.closest("tr");
      const userData = {
        Codigo: row.querySelector('[data-field="Código"]')?.textContent || row.querySelector('td:first-child').textContent,
        Nombre: row.querySelectorAll('td')[1].textContent,
        Apellido: row.querySelectorAll('td')[2].textContent,
        Correo: row.querySelectorAll('td')[3].textContent,
        Nombre_Rol: row.querySelectorAll('td')[4].textContent,
        departamento: row.querySelectorAll('td')[5].textContent
      };

      cargarDatosUsuarioEnModal(userData);
    });
  });

  // Eventos del modal
  addButton.onclick = mostrarModal;
  cerrarModal.onclick = ocultarModal;
  window.onclick = function(event) {
    if (event.target === nuevoUsuarioModal) {
      ocultarModal();
    }
  };

  // Envío del formulario
  nuevoUsuarioForm.onsubmit = function (event) {
    event.preventDefault();

    const mode = nuevoUsuarioModal.getAttribute('data-mode');
    
    if (mode === 'edit') {
      // Lógica de edición
      const datos = {
        id: document.getElementById('usuarioIdEdicion').value,
        Codigo: document.getElementById('codigo').value, // Añadir esta línea
        Nombre: document.getElementById('nombre').value,
        Apellido: document.getElementById('apellido').value,
        Correo: document.getElementById('correo').value,
        Rol: document.getElementById('rol').value,
        Departamento: document.getElementById('departamento').value
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
              popup: 'high-z-index'
            }
          }).then(() => {
            reloadPage();
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message,
            customClass: {
              popup: 'high-z-index'
            }
          });
        }
      });
    } else {
      // Lógica de creación de usuario
      const datos = {
        codigo: document.getElementById('codigo').value,
        nombre: document.getElementById('nombre').value,
        apellido: document.getElementById('apellido').value,
        correo: document.getElementById('correo').value,
        rol: document.getElementById('rol').value,
        departamento: document.getElementById('departamento').value,
        genero: document.getElementById('genero').value,
        password: document.getElementById('password').value
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
              popup: 'high-z-index'
            }
          }).then(() => {
            reloadPage();
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message,
            customClass: {
              popup: 'high-z-index'
            }
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
            popup: 'high-z-index'
          }
        });
      });
    }
  };

  function reloadPage() {
    window.location.reload();
  }
});