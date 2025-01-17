// Selecciona todos los elementos con la clase 'delete' y les agrega un listener para el evento 'click'
document.querySelectorAll('.delete').forEach(btn => {
  btn.addEventListener('click', async function(e) {
    e.preventDefault(); // Previene la acción predeterminada del botón, como el envío de formularios
    
    // Obtiene la fila más cercana al botón de eliminación y el ID del usuario a eliminar
    const row = this.closest('tr');
    const userId = row.dataset.id;

    Swal.fire({
      title: "Estás a punto de eliminar el usuario",
      text: "¿Estás seguro?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Confirmar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: "Procesando...",
          text: "Por favor espere",
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });
        
        // Realiza una solicitud POST al servidor para eliminar el usuario
        fetch('./functions/admin-usuarios/eliminarusuario.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ id: userId }) // Envía el ID del usuario en el cuerpo de la solicitud
        })
        .then(response => {
          // Procesa la respuesta del servidor como texto, luego intenta convertirlo a JSON
          return response.text().then(text => {
            console.log("Respuesta raw del servidor:", text);
            try {
              return JSON.parse(text);
            } catch (e) {
              throw new Error(`Respuesta no válida del servidor: ${text}`);
            }
          });
        })
        .then(data => {
          console.log("Datos procesados:", data);
          if (data.success) {
            Swal.fire(
              "Eliminado",
              "El usuario ha sido eliminado.",
              "success"
            ).then(() => {
              row.remove(); // Elimina la fila de la tabla correspondiente al usuario eliminado
            });
          } else {
            throw new Error(data.message || "Error desconocido en el servidor");
          }
        })
        .catch(error => {
          // Manejo de errores en la solicitud o en la respuesta
          console.error("Error completo:", error);
          Swal.fire(
            "Error!",
            "Error al eliminar el usuario. Detalles: " + error.message,
            "error"
          );
        });
      }
    });
  });
});