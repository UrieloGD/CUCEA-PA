function eliminarRegistrosSeleccionados() {
  // Obtener referencia a la tabla Tabulator
  const table = window.tabulatorTable;
  
  // Obtener las filas seleccionadas usando la API de Tabulator
  const selectedRows = table.getSelectedRows();
  const ids = selectedRows.map(row => row.getData().ID);
  
  if (ids.length === 0) {
    Swal.fire({
      title: "Advertencia",
      text: "No hay registros seleccionados. Seleccione al menos un registro para eliminar.",
      icon: "warning",
    });
    return;
  }

  Swal.fire({
    title: "¿Desea continuar?",
    text: "Se eliminarán " + ids.length + " registro(s)",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      var xhr = new XMLHttpRequest();
      xhr.open(
        "POST",
        "./functions/coord-personal-plantilla/eliminar-registros-coord.php",
        true
      );
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
          Swal.fire({
            title: "¡Éxito!",
            text: "Los registros se han eliminado correctamente.",
            icon: "success",
          }).then(() => {
            // Recargar los datos en la tabla sin recargar toda la página
            table.setPage(1).then(() => table.replaceData());
          });
        }
      };

      xhr.send("ids=" + encodeURIComponent(ids.join(",")));
    }
  });
}