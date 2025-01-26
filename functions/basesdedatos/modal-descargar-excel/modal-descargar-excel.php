<div id="modal-descargar" class="modal">
    <div class="modal-content-descarga">
    <span class="close" onclick="cerrarDescargarExcel()">&times;</span>
        <h3>Selecciona las columnas a descargar</h3>
        <div id="opciones-columnas">
            <div class="select-all-div">
                <div class="select-first">
                    <input class="input-check" type="checkbox" id="select-all-main">
                    <label for="select-all" class="select-all-div-label">Seleccionar/Deseleccionar todas</label>
                </div>
                <div class="columns-container-js">
                    <div class="label-materia">
                        <label class="encabezado-js">Materia</label>
                    </div>
                    <div class="select-all-materia">
                        <input class="input-check" type="checkbox" id="select-all-materia">
                        <label class="select-all-materia">Seleccionar/Deseleccionar grupo completo de Materia</label>
                    </div>
                    <div class="columns-container-materia">
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-ciclo-" name="columnas[]" value="CICLO">
                            <label for="col-ciclo-">CICLO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-crn-" name="columnas[]" value="CRN">
                            <label for="col-crn-">CRN</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-materia-" name="columnas[]" value="MATERIA">
                            <label for="col-materia-">MATERIA</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-cve-materia-" name="columnas[]" value="CVE MATERIA">
                            <label for="col-cve-materia-">CVE MATERIA</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-sección-" name="columnas[]" value="SECCIÓN">
                            <label for="col-sección-">SECCIÓN</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-nivel-" name="columnas[]" value="NIVEL">
                            <label for="col-nivel-">NIVEL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-nivel-tipo-" name="columnas[]" value="NIVEL TIPO">
                            <label for="col-nivel-tipo-">NIVEL TIPO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-tipo-" name="columnas[]" value="TIPO">
                            <label for="col-tipo-">TIPO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-c.-min-" name="columnas[]" value="C. MIN">
                            <label for="col-c.-min-">C. MIN</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-status-" name="columnas[]" value="STATUS">
                            <label for="col-status-">STATUS</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-l-" name="columnas[]" value="L">
                            <label for="col-l-">L</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-m-" name="columnas[]" value="M">
                            <label for="col-m-">M</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-i-" name="columnas[]" value="I">
                            <label for="col-i-">I</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-j-" name="columnas[]" value="J">
                            <label for="col-j-">J</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-v-" name="columnas[]" value="V">
                            <label for="col-v-">V</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-s-" name="columnas[]" value="S">
                            <label for="col-s-">S</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-d-" name="columnas[]" value="D">
                            <label for="col-d-">D</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-día-presencial-" name="columnas[]" value="DÍA PRESENCIAL">
                            <label for="col-día-presencial-">DÍA PRESENCIAL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-día-virtual-" name="columnas[]" value="DÍA VIRTUAL">
                            <label for="col-día-virtual-">DÍA VIRTUAL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-modalidad-" name="columnas[]" value="MODALIDAD">
                            <label for="col-modalidad-">MODALIDAD</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-fecha-inicial-" name="columnas[]" value="FECHA INICIAL">
                            <label for="col-fecha-inicial-">FECHA INICIAL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-fecha-final-" name="columnas[]" value="FECHA FINAL">
                            <label for="col-fecha-final-">FECHA FINAL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-hora-inicial-" name="columnas[]" value="HORA INICIAL">
                            <label for="col-hora-inicial-">HORA INICIAL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-hora-final-" name="columnas[]" value="HORA FINAL">
                            <label for="col-hora-final-">HORA FINAL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-módulo-" name="columnas[]" value="MÓDULO">
                            <label for="col-módulo-">MÓDULO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-aula-" name="columnas[]" value="AULA">
                            <label for="col-aula-">AULA</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-cupo-" name="columnas[]" value="CUPO">
                            <label for="col-cupo-">CUPO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-observaciones-" name="columnas[]" value="OBSERVACIONES">
                            <label for="col-observaciones-">OBSERVACIONES</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-extraordinario-" name="columnas[]" value="EXTRAORDINARIO">
                            <label for="col-extraordinario-">EXTRAORDINARIO</label>
                        </div>
                    </div>
                </div>
                <div class="columns-container-js">
                    <div class="label-profesorado">
                        <label class="encabezado-js">Profesorado</label>
                    </div>
                    <div class="select-all-profesor">
                        <input class="input-check" type="checkbox" id="select-all-profesor">
                        <label for="select-all-profesor">Seleccionar/Deseleccionar grupo completo de Profesorado</label>
                    </div>
                    <div class="columns-container-profesor">
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-tipo-contrato-" name="columnas[]" value="TIPO CONTRATO ">
                            <label for="col-tipo-contrato-">TIPO CONTRATO </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-código-" name="columnas[]" value="CÓDIGO ">
                            <label for="col-código-">CÓDIGO </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-nombre-profesor-" name="columnas[]" value="NOMBRE PROFESOR ">
                            <label for="col-nombre-profesor-">NOMBRE PROFESOR </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-categoria-" name="columnas[]" value="CATEGORIA ">
                            <label for="col-categoria-">CATEGORIA </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-descarga-" name="columnas[]" value="DESCARGA ">
                            <label for="col-descarga-">DESCARGA </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-código-descarga-" name="columnas[]" value="CÓDIGO DESCARGA ">
                            <label for="col-código-descarga-">CÓDIGO DESCARGA </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-nombre-descarga-" name="columnas[]" value="NOMBRE DESCARGA ">
                            <label for="col-nombre-descarga-">NOMBRE DESCARGA </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-nombre-definitivo-" name="columnas[]" value="NOMBRE DEFINITIVO ">
                            <label for="col-nombre-definitivo-">NOMBRE DEFINITIVO </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-titular-" name="columnas[]" value="TITULAR ">
                            <label for="col-titular-">TITULAR </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-horas-" name="columnas[]" value="HORAS ">
                            <label for="col-horas-">HORAS </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-código-dependencia-" name="columnas[]" value="CÓDIGO DEPENDENCIA ">
                            <label for="col-código-dependencia-">CÓDIGO DEPENDENCIA </label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-h.-totales-" name="columnas[]" value="H. TOTALES ">
                            <label for="col-h.-totales-">H. TOTALES </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fila-botones">
            <button onclick="descargarExcelSeleccionado()">Descargar seleccion</button>
            <?php if ($_SESSION['Rol_ID'] == 2): ?>
                <button class="btn-cotejo" onclick="descargarExcelCotejado()">Descargar cotejo</button>
            <?php endif; ?>
            <!-- <button onclick="cerrarPopupColumnas()">Cancelar</button> -->
        </div>
    </div>
</div>    