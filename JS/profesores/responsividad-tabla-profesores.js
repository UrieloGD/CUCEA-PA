window.addEventListener('resize', function() {
    const anchoPantalla = window.innerWidth;

    // Elementos de barra color azul
    let titleCategoria = document.getElementById("title-categoria");
    let titleHorasF = document.getElementById("title-horas-f");
    let titleHorasD = document.getElementById("title-horas-d");
    let titleHorasT = document.getElementById("title-horas-t");

    // Elementos de detalle tabla
    let detalleCategoria = document.querySelectorAll("td detalle-column col-categoria");
    let detalleHorasF = document.getElementsByClassName("col-categoria");
    let detalleHorasD = document.getElementsByClassName("col-categoria");
    let detalleHorasT = document.getElementsByClassName("col-categoria");

    if (anchoPantalla <= 996) {
        titleCategoria.style.display = "none";
        titleHorasF.style.display = "none";
        titleHorasD.style.display = "none";
        titleHorasT.style.display = "none";

        detalleCategoria.forEach(function(dtCategoria) {
            dtCategoria.style.display = "none";
        });
        detalleHorasF.style.display = "none";
        detalleHorasD.style.display = "none";
        detalleHorasT.style.display = "none";
    } else {
        titleCategoria.style.display = "";
        titleHorasF.style.display = "";
        titleHorasD.style.display = "";
        titleHorasT.style.display = "";

        detalleCategoria.style.display = "";
        detalleHorasF.style.display = "";
        detalleHorasD.style.display = "";
        detalleHorasT.style.display = "";
    }
});