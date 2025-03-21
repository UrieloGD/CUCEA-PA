<?php
function generarDepartamento($depto)
{
    $esArriba = $depto['es_arriba'] ? 'true' : 'false';

    $nombreLineas = implode('', array_map(function ($linea, $index) use ($depto) {
        // Solo aplicar estilos si el departamento es multilínea
        if ($depto['multilinea']) {
            $bottom = ($index == 0) ? '-5px' : '-3px'; // Primera línea 10px, segunda 30px
            return "<p style='position:relative; bottom:{$bottom}; margin:0;'>$linea</p>";
        }
        return "<p>$linea</p>"; // Sin estilos si el departamento no es multilínea
    }, $depto['nombre_lineas'], array_keys($depto['nombre_lineas']))); // Pasamos el índice

    return <<<HTML
    <div class="contenedor-de-contenedores">
    <div class="departamento-contenedor-principal"
        onclick="mostrarInformacion('contenedor-informacion-{$depto['id']}', this.querySelector('.icono-despliegue i'), $esArriba)">
        <div class="espacio-icono">
            <img class="icono-dpto"
                src="./Img/Icons/iconos-horas-comparacion/departamentos/{$depto['imagen']}"
                alt="{$depto['nombre']}"
                style="{$depto['style_imagen']}">
        </div>
        <div class="titulo-dpto">
            $nombreLineas
        </div>
        <div class="icono-despliegue">
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </div>
    </div>
    <div class="contenedor-informacion" id="contenedor-informacion-{$depto['id']}">
        <div class="hrs-totales-dpto_container">
            <p class="titulo-totales-dpto">Horas totales</p>
            <div class="tipo-hora-selector">
                <button class="tipo-hora-btn active" data-tipo="frente-grupo">Frente Grupo</button>
                <button class="tipo-hora-btn" data-tipo="definitivas">Definitivas</button>
                <button class="tipo-hora-btn" data-tipo="temporales">Temporales</button>
            </div>
            <div class="borde-barra-stats-hrs" style="border: 3px solid {$depto['color']};">
                <div class="barra-stats-hrs" style="background-color: {$depto['color']};">
                    <p class="porcentaje-dpto"
                        style="color: white; font-weight: bold; margin: 0; position: absolute; width: 100%; text-align: center; line-height: 30px;">
                        60%
                    </p>
                </div>
            </div>
            <p class="horas-comp-dpto">Cargando datos...</p>
            <div class="titulo-underline" style="width:100%;"></div>
        </div>
        <div class="ultima-mod-dpto_container">
            <p class="titulo-totales-dpto">Última modificación</p>
            <table class="tabla-ultimas-mod-dpto">
                <thead class="encabezado-ultimas-mod-dpto" style="background-color: {$depto['color']};">
                    <tr>
                        <td>Fecha</td>
                        <td>Hora</td>
                        <td>Responsable</td>
                    </tr>
                </thead>
                <tbody class="cuerpo-ultimas-mod-dpto">
                    <tr>
                        <td>23/10/24</td>
                        <td>13:00</td>
                        <td>Rafael Castanedo Escobedo</td>
                    </tr>
                </tbody>
            </table>
            <button class="desglose-button-dpto"
                data-departamento="{$depto['nombre']}"
                style="background-color: {$depto['color']};">Desglose</button>
        </div>
    </div>
    </div>
    HTML;
}
