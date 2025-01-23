// Obtener el elemento de búsqueda
const searchInput = document.getElementById("search-input");

// Agregar evento que escucha los cambios en el campo de búsqueda
searchInput.addEventListener("input", function (e) {
  const terminoBusqueda = e.target.value.toLowerCase();
  // Seleccionar todas las filas de la tabla excepto la primera
  const filasTabla = document.querySelectorAll("table tr:not(:first-child)");

  filasTabla.forEach((fila) => {
    let texto = "";
    fila.querySelectorAll("td").forEach((celda) => {
      texto += celda.textContent.toLowerCase() + " ";
    });
    // Verificar si el texto acumulado de la fila contiene el término de búsqueda
    if (texto.includes(terminoBusqueda)) {
      fila.style.display = "";
    } else {
      fila.style.display = "none";
    }
  });
});
