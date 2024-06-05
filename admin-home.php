<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Centro de Gestión</title>
<link rel="stylesheet" href="./CSS/admin-home.css"/>

<!--Cuadro principal del home-->
<div class="cuadro-principal">

    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Gestión de usuarios</h3>
        </div>
    </div>

    <!-- Recuadros superiores -->
    <div class="recuadros-superiores">
        <div class="recuadro active" onclick="activateRecuadro(this)">
            <img src="./Img/img-admin/img-reporte-entrega.jpg" alt="Reporte de entrega">
            <div class="texto">Reporte de entrega</div>
        </div>
        <div class="recuadro" onclick="activateRecuadro(this)">
            <img src="./Img/img-admin/img-control-eventos.jpg" alt="Control de eventos">
            <div class="texto">Control de eventos</div>
        </div>
        <div class="recuadro" onclick="activateRecuadro(this)">
            <img src="./Img/img-admin/img-gestion-usuarios.jpeg" alt="Gestión de usuarios">
            <div class="texto">Gestión de usuarios</div>
        </div>
    </div>

    <div class="contenido">
        <div class="izquierda">
          <h3 class="titulo-tabla">Bases de Datos Pendientes de Entrega</h3>
          <table class="tabla">
                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th>Estado de la entrega</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Estudios Regionales</td>
                        <td class="estado-sin-entregar">Sin entregar</td>
                    </tr>
                    <tr>
                        <td>Ciencias Sociales</td>
                        <td class="estado-atrasada">Atrasada</td>
                    </tr>
                    <tr>
                        <td>PALE</td>
                        <td class="estado-sin-entregar">Sin entregar</td>
                    </tr>
                    <tr>
                        <td>Posgrados</td>
                        <td class="estado-sin-entregar">Sin entregar</td>
                    </tr>
                    <tr>
                        <td>Economía</td>
                        <td class="estado-atrasada">Atrasada</td>
                    </tr>
                    <tr>
                        <td>Recursos Humanos</td>
                        <td class="estado-atrasada">Atrasada</td>
                    </tr>
                    <tr>
                        <td>Métodos Cuantitativos</td>
                        <td class="estado-sin-entregar">Sin entregar</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="derecha">
            <h3>Progreso de Entregas</h3>
            <div class="progreso">
                <p>Se ha alcanzado el 70% del total de entregas necesarias.</p>
                <div class="circulo">70%</div>
            </div>
        </div>
    </div>
</div>


<?php include './template/footer.php' ?>

<!-- <script>
function activateRecuadro(element) {
    var recuadros = document.querySelectorAll('.recuadro');
    recuadros.forEach(function(recuadro) {
        recuadro.classList.remove('active');
        recuadro.classList.add('inactive');
    });
    element.classList.add('active');
    element.classList.remove('inactive');
}
</script> -->

<?php include './template/footer.php' ?>
