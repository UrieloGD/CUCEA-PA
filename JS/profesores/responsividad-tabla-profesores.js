function ajustarColumnas() {
    const anchoPantalla = window.innerWidth;

    // Elementos de barra color azul
    let titleCategoria = document.getElementById("title-categoria");
    let titleHorasF = document.getElementById("title-horas-f");
    let titleHorasD = document.getElementById("title-horas-d");
    let titleHorasT = document.getElementById("title-horas-t");

    // Elementos de detalle tabla
    let detalleCategoria = document.querySelectorAll("td.detalle-column.col-categoria");
    let detalleHorasF = document.getElementsByClassName("col-horas-f");
    let detalleHorasD = document.getElementsByClassName("col-horas-d");
    let detalleHorasT = document.getElementsByClassName("col-horas-t");

    if (anchoPantalla <= 996) {
        titleCategoria.style.display = "none";
        titleHorasF.style.display = "none";
        titleHorasD.style.display = "none";
        titleHorasT.style.display = "none";

        Array.from(detalleCategoria).forEach(element => element.style.display = "none");
        Array.from(detalleHorasF).forEach(element => element.style.display = "none");
        Array.from(detalleHorasD).forEach(element => element.style.display = "none");
        Array.from(detalleHorasT).forEach(element => element.style.display = "none");

    } else {
        titleCategoria.style.display = "";
        titleHorasF.style.display = "";
        titleHorasD.style.display = "";
        titleHorasT.style.display = "";

        Array.from(detalleCategoria).forEach(element => element.style.display = "");
        Array.from(detalleHorasF).forEach(element => element.style.display = "");
        Array.from(detalleHorasD).forEach(element => element.style.display = "");
        Array.from(detalleHorasT).forEach(element => element.style.display = "");
    }
}

// Ejecutar la función al cargar la página
window.addEventListener("DOMContentLoaded", ajustarColumnas);
window.addEventListener("load", ajustarColumnas);

// También ejecutarla al redimensionar la ventana
window.addEventListener("resize", ajustarColumnas);