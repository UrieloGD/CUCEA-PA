/* Script para desplegable de departamentos */
let contenedorActual = null;
let iconoActual = null;

function mostrarInformacion(contenedorId, icono, esArriba = false) {
  const nuevoContenedor = document.getElementById(contenedorId);

  if (contenedorActual && contenedorActual !== nuevoContenedor) {
    contenedorActual.classList.remove("mostrar");
    contenedorActual.classList.remove("desplegable-arriba");

    // Remover clases de rotacion del icono anterior
    iconoActual.classList.remove("rotar");
    iconoActual.classList.remove("rotar-arriba");
  }

  // Alternar mostrar/ocultar
  nuevoContenedor.classList.toggle("mostrar");

  // Manejar la rotación segun la dirección
  if (esArriba) {
    // Si es hacia arriba y se está mostrando
    if (nuevoContenedor.classList.contains("mostrar")) {
      nuevoContenedor.classList.add("desplegable-arriba");
      icono.classList.add("rotar-arriba");
    } else {
      // Si se está ocultando, quitar las clases
      nuevoContenedor.classList.remove("desplegable-arriba");
      icono.classList.remove("rotar-arriba");
    }
  } else {
    // Para contenedores hacia abajo
    icono.classList.toggle("rotar");
  }

  if (nuevoContenedor.classList.contains("mostrar")) {
    contenedorActual = nuevoContenedor;
    iconoActual = icono;
  } else {
    contenedorActual = null;
    iconoActual = null;
  }
}
