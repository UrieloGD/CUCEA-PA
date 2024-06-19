<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Centro de Gestión</title>
<link rel="stylesheet" href="./CSS/admin-visual-eventos.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">

    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Próximos Eventos</h3>
        </div>
    </div>

    <div>
</div>

<div class="section-title">Esta semana</div>

<!-- Contenido -->
<div class="event-container">
<div class="event-header">
<div class="event-day-container">
  <div class="event-day">Hoy</div>
  <div class="event-time">15:00</div>
</div>
</div>
  <div class="event-details">
    <h3>Reunión de apertura de Programación Académica</h3>
    <p>Se presentarán las metas y objetivos para el año, se discutirán las estrategias de implementación y se asignarán roles y responsabilidades. Este evento es fundamental para coordinar los esfuerzos del equipo y asegurar una programación académica efectiva y bien organizada.</p>
    <div class="event-icons">
      <span class="icon icon-red">M</span>
      <span class="icon icon-green">J</span>
      <span class="icon icon-blue">G</span>
      <span class="icon icon-pink">L</span>
      <span class="icon icon-orange">A</span>
      <span class="icon icon-purple">R</span>
      <span class="icon icon-fuchsia">K</span>
      <span class="icon icon-teal">P</span>
      <span class="icon icon-brown">S</span>
    </div>
    <div class="event-footer">
      <span class="department">Jefes de Departamento</span>
      <span class="coordination">Coordinación de personal</span>
    </div>
  </div>
</div> 

 <!-- Proximos mes --> 

 <div class="section-title">Proximo mes</div>

 <div class="event-container">
 <div class="event-header">
 <div class="event-day-container">
  <div class="event-day">02/04/2024</div>
  <div class="event-time">23:59</div>
</div>
</div>
  <div class="event-details">
    <h3>Entrega de bases de datos</h3>
    <p>Se presentarán las nuevas versiones de los datos, se explicarán las mejoras realizadas y se proporcionarán instrucciones detalladas para su uso adecuado. Este evento garantiza que todos los miembros del equipo tengan acceso a la información más reciente y precisa, facilitando así el cumplimiento de nuestros objetivos.</p>
    <div class="event-icons">
      <span class="icon icon-red">M</span>
      <span class="icon icon-green">J</span>
      <span class="icon icon-blue">G</span>
      <span class="icon icon-pink">L</span>
    </div>
    <div class="event-footer">
      <span class="department">Jefes de Departamento</span>
      <span class="coordination">Coordinación de personal</span>
    </div>
  </div>
</div> 

  <!-- -->
<div class="event-container">
<div class="event-header">
<div class="event-day-container">
  <div class="event-day">21/04/2024</div>
  <div class="event-time">18:00</div>
</div>
</div>
  <div class="event-details">
    <h3>Reunión de revisión de bases de datos</h3>
    <p>Esta reunión es esencial para el buen funcionamiento de nuestras actividades académicas, ya que una base de datos precisa es fundamental para la toma de decisiones informadas y la planificación estratégica. Invitamos a todos los miembros del equipo a participar activamente y contribuir con su experiencia y conocimientos.</p>
    <div class="event-icons">
      <span class="icon icon-red">M</span>
      <span class="icon icon-green">J</span>
      <span class="icon icon-blue">G</span>
      <span class="icon icon-pink">L</span>
    </div>
    <div class="event-footer">
      <span class="department">Jefes de Departamento</span>
      <span class="coordination">Coordinación de personal</span>
    </div>
  </div>
</div> 

<?php include './template/footer.php' ?>