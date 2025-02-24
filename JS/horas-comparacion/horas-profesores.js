// Obtener el modal
const modal = document.querySelector(".principal");

// Obtener todos los enlaces de departamentos
const enlacesDepartamentos = document.querySelectorAll(
  ".contenedor-departamentos a"
);

// Obtener el elemento <span> que cierra el modal
const spanCerrar = document.createElement("span");
spanCerrar.textContent = "x";
spanCerrar.style.position = "absolute";
spanCerrar.style.top = "10px";
spanCerrar.style.right = "10px";
spanCerrar.style.fontSize = "30px";
spanCerrar.style.cursor = "pointer";
modal.appendChild(spanCerrar);

// Cuando el usuario hace clic en un enlace de departamento, abrir el modal
enlacesDepartamentos.forEach((enlace) => {
  enlace.onclick = function (e) {
    e.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
    modal.style.display = "block";

    // Actualizar el título del modal con el nombre del departamento
    const nombreDepto = this.querySelector(".dept").textContent;
    document.querySelector(".principal .titulo").textContent = nombreDepto;
  };
});

// Cuando el usuario hace clic en <span> (x), cerrar el modal
spanCerrar.onclick = function () {
  modal.style.display = "none";
};

// Cuando el usuario hace clic en cualquier lugar fuera del modal, cerrarlo
window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};

// Función para alternar la barra de búsqueda
const alternarBusqueda = () => {
  const entradaBusqueda = document.querySelector(".input-barra-hidden");
  entradaBusqueda.style.display =
    entradaBusqueda.style.display === "none" ? "inline-block" : "none";
};

// Agregar evento de clic al ícono de búsqueda
document
  .getElementById("icono-buscador")
  .addEventListener("click", alternarBusqueda);

// Función para manejar filtros (placeholder)
const manejarFiltros = () => {
  console.log("Filtros clicados");
  // Agrega tu lógica de filtrado aquí
};

// Agregar evento de clic al ícono de filtros
document
  .getElementById("icono-filtros")
  .addEventListener("click", manejarFiltros);

// Inicialmente ocultar el modal
modal.style.display = "none";

// Agregar algunos estilos básicos para que el modal aparezca como un popup
modal.style.position = "fixed";
modal.style.zIndex = "1001";
modal.style.left = "50%";
modal.style.top = "50%";
modal.style.transform = "translate(-50%, -50%)";
modal.style.boxShadow = "0 4px 8px rgba(0,0,0,0.1)";

// Obtener la superposición
const superposicion = document.querySelector(".overlay");

// Cuando el usuario hace clic en un enlace de departamento, abrir el modal
enlacesDepartamentos.forEach((enlace) => {
  enlace.onclick = function (e) {
    e.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
    modal.style.display = "block";
    superposicion.style.display = "block"; // Mostrar superposición

    // Actualizar el título del modal con el nombre del departamento
    const nombreDepto = this.querySelector(".dept").textContent;
    document.querySelector(".principal .titulo").textContent = nombreDepto;
  };
});

// Cuando el usuario hace clic en <span> (x) o en la superposición, cerrar el modal
spanCerrar.onclick = function () {
  modal.style.display = "none";
  superposicion.style.display = "none"; // Ocultar superposición
};

// Cuando el usuario hace clic en cualquier lugar fuera del modal, cerrarlo
window.onclick = function (event) {
  if (event.target == modal || event.target == superposicion) {
    modal.style.display = "none";
    superposicion.style.display = "none"; // Ocultar superposición
  }
};
