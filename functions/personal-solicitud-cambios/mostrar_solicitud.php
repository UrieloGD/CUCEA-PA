<!--./functions/personal-solicitud_cambios/mostrar_solicitud.php -->
<div class="info-sup" onclick="mostrarInformacion('contenedor-informacion-<?php echo $index; ?>', this.querySelector('.icono-despliegue i'))">
    <!-- Color y tipo de solicitud -->
    <div class="color-<?php echo strtolower(str_replace(' ', '_', $solicitud['estado'])); ?>" style="position: absolute; left: 0px;"></div>
    <div class="nombre-dpto-solicitud">
        <span>Tipo de solicitud:</span>
        <span class="nombre-solicitud"><?php echo $solicitud['tipo']; ?></span>
    </div>
    <div class="nombre-dpto-solicitud">
        <span>Departamento:</span>
        <span class="nombre-departamento">
            <?php 
            // Convertir a mayúsculas manteniendo acentos y caracteres especiales
            $departamento = mb_strtoupper(
                str_replace('_', ' ', $solicitud['departamento']), 
                'UTF-8'
            );
            echo trim($departamento);
            ?>
        </span>
    </div>
    <hr style="border: 1px solid #ccc; margin: 10px 0;">
    
    <!-- Fecha, hora y estado -->
    <div class="fecha-hora-status">
        <span class="fecha-solicitud">
            <span style="margin-left: 0; font-weight: 300;">Fecha:</span> 
            <?php echo $solicitud['fecha']; ?>
        </span>
        <span class="hora-solicitud">
            <span style="margin-left: 0; font-weight: 300;">Hora:</span> 
            <?php echo $solicitud['hora']; ?>
        </span>
        <div class="circulo-<?php echo strtolower(str_replace(' ', '_', $solicitud['estado'])); ?>">
            <i class="fa fa-circle" aria-hidden="true">
                <span class="estado-solicitud" style="margin-left: 10px;">
                    <?php echo $solicitud['estado']; ?>
                </span>
            </i>
        </div>
    </div>

    <div class="icono-despliegue">
        <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
    </div>
</div>

<!-- Información detallada -->
<div class="contenedor-informacion" id="contenedor-informacion-<?php echo $index; ?>">
    <div class="titulo-info">
        <p>Información </p>
    </div>
    <div class="info">
        <div class="contenedor-izquierdo">
            <?php if($solicitud['tipo'] == 'Solicitud de baja-propuesta'): ?>
                <p class="materia"><strong>Nombre de materia:</strong> <?php echo $solicitud['materia']; ?></p>
            <?php else: ?>
                <p class="puesto">Puesto: <p id="info-puesto"><?php echo $solicitud['puesto']; ?></p></p>
            <?php endif; ?>
            <p class="CRN">CRN: <p id="info-CRN"><?php echo $solicitud['crn']; ?></p></p>
        </div>
        <div class="contenedor-centro">
            <p class="folio">Folio de solicitud: <p id="info-folio"><?php echo $solicitud['folio']; ?></p></p>
            <?php if($solicitud['tipo'] == 'Solicitud de baja'): ?>
                <p class="efecto">Sin efectos desde: <p id="info-efecto"><?php echo $solicitud['efecto']; ?></p></p>
            <?php endif; ?>
            <?php if($solicitud['tipo'] == 'Solicitud de baja-propuesta'): ?>
                <p class="clave">Clave: <p id="info-clave"><?php echo isset($solicitud['clave']) ? $solicitud['clave'] : ''; ?></p></p>
            <?php endif; ?>
            <?php if($solicitud['tipo'] == 'Solicitud de propuesta'): ?>
                <p class="clasificacion_p">Clasificación: <p id="info-clasificacion_p"><?php echo isset($solicitud['clasificacion_p']) ? $solicitud['clasificacion_p'] : ''; ?></p></p>
            <?php endif; ?>
        </div>
        <div class="contenedor-derecha">
            <?php if($solicitud['tipo'] == 'Solicitud de baja'): ?>
                <p class="clasificacion_b">Clasificación: <p id="info-clasificacion_b"><?php echo $solicitud['clasificacion_b']; ?></p></p>
            <?php endif; ?>
            <?php if($solicitud['tipo'] == 'Solicitud de propuesta'): ?>
                <p class="horas_sem">Horas Semanales: <p id="info-horas_sem"><?php echo isset($solicitud['horas_sem']) ? $solicitud['horas_sem'] : ''; ?></p></p>
            <?php endif; ?>
            <?php if($solicitud['tipo'] == 'Solicitud de propuesta'): ?>
                <p class="periodo_desde">Periodo asignado desde - hasta: <p id="info-periodo_desde"><?php echo isset($solicitud['periodo_desde']) ? $solicitud['periodo_desde'] : '' ;
                 ?></p></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Información del profesor actual -->
    <div class="titulo-info">
        <p>Profesor actual</p>
    </div>
    <div class="info">
        <div class="contenedor-izquierdo">
            <p class="paterno">Apellido paterno: <p id="info-paterno"><?php echo $solicitud['profesor_actual']['paterno']; ?></p></p>
            <p class="materno">Apellido materno: <p id="info-materno"><?php echo $solicitud['profesor_actual']['materno']; ?></p></p>
        </div>
        <div class="contenedor-centro">
            <p class="nombres">Nombre(s): <p id="info-nombres"><?php echo $solicitud['profesor_actual']['nombres']; ?></p></p>
            <p class="codigo">Código Profesor: <p id="info-codigo"><?php echo $solicitud['profesor_actual']['codigo']; ?></p></p>
        </div>
        <div class="contenedor-derecho">
            <p class="motivo">Motivo: <p id="info-motivo"><?php echo $solicitud['motivo']; ?></p></p>
        </div>
    </div>

    <?php if(isset($solicitud['profesor_propuesto'])): ?>
    <!-- Información del profesor propuesto -->
    <div class="titulo-info">
        <p>Profesor propuesto</p>
    </div>
    <div class="info">
        <div class="contenedor-izquierdo">
            <p class="paterno">Apellido paterno: <p id="info-paterno-propuesto"><?php echo $solicitud['profesor_propuesto']['paterno']; ?></p></p>
            <p class="materno">Apellido materno: <p id="info-materno-propuesto"><?php echo $solicitud['profesor_propuesto']['materno']; ?></p></p>
        </div>
        <div class="contenedor-derecho">
            <p class="nombres">Nombre(s): <p id="info-nombres-propuesto"><?php echo $solicitud['profesor_propuesto']['nombres']; ?></p></p>
            <p class="codigo">Código Profesor: <p id="info-codigo-propuesto"><?php echo $solicitud['profesor_propuesto']['codigo']; ?></p></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="contenedor-botones">
        <?php 
        // Determinar el tipo de solicitud normalizado
        $tipo = strtolower(str_replace(['Solicitud de ', ' '], ['', '-'], $solicitud['tipo']));
        ?>
        
        <!-- Botón para ver detalles completos -->
        <button class="boton-ver-detalles" 
            data-folio="<?php echo $solicitud['folio']; ?>" 
            data-tipo="<?php echo $tipo; ?>">
            <i class="fas fa-eye"></i> Ver detalles
        </button>
        
        <?php if ($_SESSION['Rol_ID'] == 3): ?>
            <?php if ($solicitud['estado'] == 'Pendiente'): ?>
                <button class="boton-generar" 
                        data-folio="<?= $solicitud['folio'] ?>" 
                        data-tipo="<?= $tipo ?>">
                    <i class="fas fa-file-pdf"></i> Generar PDF
                </button>
            
            <?php elseif ($solicitud['estado'] == 'En revision'): ?>
                <button class="boton-descargar" 
                        data-folio="<?= $solicitud['folio'] ?>" 
                        data-tipo="<?= $tipo ?>">
                    <i class="fas fa-download"></i> Descargar PDF
                </button>
                
                <!-- Botones de aceptar/rechazar para rol 3 -->
                <button class="boton-aceptar" 
                        data-folio="<?= $solicitud['folio'] ?>" 
                        data-tipo="<?= $tipo ?>">
                    <i class="fas fa-check"></i> Aceptar
                </button>
                
                <button class="boton-rechazar" 
                        data-folio="<?= $solicitud['folio'] ?>" 
                        data-tipo="<?= $tipo ?>">
                    <i class="fas fa-times"></i> Rechazar
                </button>
            <?php endif; ?>
            
        <?php elseif ($solicitud['estado'] == 'En revision'): ?>
            <button class="boton-descargar" 
                    data-folio="<?= $solicitud['folio'] ?>" 
                    data-tipo="<?= $tipo ?>">
                <i class="fas fa-download"></i> Descargar PDF
            </button>
        
        <?php else: ?>
            <div class="aviso-pendiente-no-disponible">
                <i class="fas fa-info-circle"></i> PDF disponible después de revisión
            </div>
        <?php endif; ?>
    </div>
</div>


<? include('./modales/modal-comentarios.php'); ?>