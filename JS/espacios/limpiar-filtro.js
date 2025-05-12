$(document).ready(function() {
    // Evento para el botón limpiar
    $("#limpiar").click(function() {
        // Restablecer el selector de módulo
        var moduloOriginal = "CEDA";
        $("#modulo").val(moduloOriginal);
        
        // Restablecer los demás filtros
        $("#dia").val("");
        $("#horario_inicio").val("");
        $("#horario_fin").val("");
        
        // Desactivar el checkbox de tiempo real si está activado
        $("#tiempo-real").prop("checked", false);
        
        // Recargar la página para mostrar todos los espacios sin filtros
        window.location.href = "espacios.php?modulo=" + moduloOriginal;
    });
});