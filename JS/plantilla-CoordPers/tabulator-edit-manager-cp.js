class TabulatorEditManager {
  constructor() {
      console.log('[TabulatorEditManager] Constructor iniciado');
      this.changedCells = new Map(); // Almacena cambios pendientes {rowId: {column: {oldValue, newValue}}}
      this.originalData = new Map(); // Backup de datos originales
      this.table = null;
      this.userRole = null;
      this.puedeEditar = window.puedeEditar || true;
      console.log('[TabulatorEditManager] Inicialización completada');
      
      // Mapeo de columnas para la base de datos
      this.columnMap = {
          ID: "ID",
          Datos: "DATOS",
          Codigo: "CODIGO",
          Paterno: "PATERNO",
          Materno: "MATERNO",
          Nombres: "NOMBRES",
          Nombre_completo: "NOMBRE_COMPLETO",
          Departamento: "DEPARTAMENTO",
          Categoria_actual: "CATEGORIA_ACTUAL",
          Categoria_actual_dos: "CATEGORIA_ACTUAL_DOS",
          Horas_frente_grupo: "HORAS_FRENTE_GRUPO",
          Division: "DIVISION",
          Tipo_plaza: "TIPO_PLAZA",
          Cat_act: "CAT_ACT",
          Carga_horaria: "CARGA_HORARIA",
          Horas_definitivas: "HORAS_DEFINITIVAS",
          Udg_virtual_CIT: "UDG_VIRTUAL_CIT",
          Horario: "HORARIO",
          Turno: "TURNO",
          Investigacion_nombramiento_cambio_funcion: "CAMBIO_FUNCION",
          SNI: "SNI",
          SNI_desde: "SNI_DESDE",
          Cambio_dedicacion: "CAMBIO_DEDICACION",
          Telefono_particular: "TEL_PARTICULAR",
          Telefono_oficina: "TEL_OFICINA",
          Domicilio: "DOMICILIO",
          Colonia: "COLONIA",
          CP: "CODIGO_POSTAL",
          Ciudad: "CIUDAD",
          Estado: "ESTADO",
          No_imss: "NO_IMSS",
          CURP: "CURP",
          RFC: "RFC",
          Lugar_nacimiento: "LUGAR_NACIMIENTO",
          Estado_civil: "ESTADO_CIVIL",
          Tipo_sangre: "TIPO_SANGRE",
          Fecha_nacimiento: "FECHA_NACIMIENTO",
          Edad: "EDAD",
          Nacionalidad: "NACIONALIDAD",
          Correo: "CORREO",
          Correos_oficiales: "CORREOS_OFICIALES",
          Ultimo_grado: "ULTIMO_GRADO",
          Programa: "PROGRAMA",
          Nivel: "NIVEL",
          Institucion: "INSTITUCION",
          Estado_pais: "ESTADO_PAIS",
          Año: "AÑO",
          Gdo_exp: "GDO_EXP",
          Otro_grado: "OTRO_GRADO",
          Otro_programa: "OTRO_PROGRAMA",
          Otro_nivel: "OTRO_NIVEL",
          Otro_institucion: "OTRO_INSTITUCION",
          Otro_estado_pais: "OTRO_ESTADO_PAIS",
          Otro_año: "OTRO_ANO",
          Otro_gdo_exp: "OTRO_GDO_EXP",
          Otro_grado_alternativo: "OTRO_GRADO_ALT",
          Otro_programa_alternativo: "OTRO_PROGRAMA_ALT",
          Otro_nivel_altenrativo: "OTRO_NIVEL_ALT",
          Otro_institucion_alternativo: "OTRO_INSTITUCION_ALT",
          Otro_estado_pais_alternativo: "OTRO_ESTADO_PAIS_ALT",
          Otro_año_alternativo: "OTRO_ANO_ALT",
          Otro_gdo_exp_alternativo: "OTRO_GDO_EXP_ALT",
          Proesde_24_25: "PROESDE_24_25",
          A_partir_de: "A_PARTIR_DE",
          Fecha_ingreso: "FECHA_INGRESO",
          Antiguedad: "ANTIGUEDAD",
      };
      this.init();
  }
  
  // Método de inicialización
  init() {
      // Obtener elementos del DOM
      this.userRole = document.getElementById("user-role")?.value || 
      document.getElementById("departamento_id")?.getAttribute("data-user-role");

      // Inicializar cuando la tabla esté lista
      this.waitForTable();

      // Configurar eventos de los iconos
      this.setupIconEvents();

      // Inicialmente ocultar iconos
      this.hideEditIcons();
  }

  waitForTable() {
      // Esperar a que la variable 'table' global esté disponible
      const checkTable = () => {
          if (window.table && typeof window.table.on === "function") {
              this.table = window.table;
              this.setupTableEvents();
              this.backupOriginalData();
              console.log("[TabulatorEditManager] Tabla y eventos listos!");
          } else {
              setTimeout(checkTable, 100);
          }
      };
      checkTable();
  }

  setupTableEvents() {
      console.log('[TabulatorEditManager] Configurando eventos de la tabla...');
      
      if (!this.table) {
          console.error('[TabulatorEditManager] Error: La tabla no está definida');
          return;
      }

      // Evento cuando se edita una celda manualmente
      this.table.on("cellEdited", (cell) => {
          console.log('[TabulatorEditManager] Celda editada manualmente:', cell.getField(), cell.getValue());
          if (!this.puedeEditar) {
              this.showFeedbackMessage(
                  "No puedes editar fuera de las fechas de Programación Académica."
              );
              // Revertir el cambio
              const originalValue = this.getOriginalValue(cell);
              if (originalValue !== null && originalValue !== undefined) {
                  cell.setValue(originalValue);
              }
              return;
          }

          this.handleCellEdit(cell);
      });

      // NUEVO: Evento cuando se pega contenido desde el portapapeles
      this.table.on("clipboardPasted", (clipboard, rowData, rows) => {
          console.log('[TabulatorEditManager] Contenido pegado:', { clipboard, rowData, rows });
          if (!this.puedeEditar) {
              this.showFeedbackMessage(
                  "No puedes editar fuera de las fechas de Programación Académica."
              );
              // Revertir todos los cambios pegados
              this.revertPastedChanges(rows);
              return;
          }

          this.handleClipboardPaste(clipboard, rowData, rows);
      });

      // Evento cuando se inicia la edición
      this.table.on("cellEditStart", (cell) => {
          if (!this.puedeEditar) {
              cell.cancelEdit();
              return;
          }
      });

      // Evento cuando se cancela la edición
      this.table.on("cellEditCancelled", (cell) => {
          this.removeFromChanges(cell);
      });
  }

  // NUEVO: Revertir cambios pegados (si no se puede editar)
  revertPastedChanges(rows) {
      console.log('[TabulatorEditManager] Revirtiendo cambios pegados...');
      
      rows.forEach(row => {
          const currentRowData = row.getData();
          const rowId = currentRowData.ID || currentRowData.ID_Plantilla;
          const originalRowData = this.originalData.get(rowId);
          
          if (originalRowData) {
              // Restaurar cada campo a su valor original
              Object.keys(currentRowData).forEach(field => {
                  if (field !== 'ID' && field !== 'checkbox') {
                      const originalValue = originalRowData[field] !== undefined ? originalRowData[field] : "";
                      const cell = row.getCell(field);
                      if (cell) {
                          cell.setValue(originalValue);
                      }
                  }
              });
          }
      });
  }

  backupOriginalData() {
      if (!this.table) return;

      // Respaldar todos los datos originales - CORREGIDO: usar identificador consistente
      this.table.getData().forEach((row) => {
          // Usar el identificador que realmente existe en tus datos
          const rowId = row.ID || row.ID_Plantilla; // Ajusta según tu estructura real
          this.originalData.set(rowId, JSON.parse(JSON.stringify(row)));
      });
      
      console.log('[TabulatorEditManager] Datos originales respaldados:', this.originalData.size, 'filas');
  }

  handleCellEdit(cell) {
      console.log("[TabulatorEditManager] handleCellEdit llamado");
      const row = cell.getRow();
      const rowData = row.getData();
      
      // CORREGIDO: usar identificador consistente
      const rowId = rowData.ID || rowData.ID_Plantilla;
      
      console.log('[TabulatorEditManager] Datos de la fila editada:', rowId);
      const field = cell.getField();
      const newValue = cell.getValue();
      const originalValue = this.getOriginalValue(cell);

      console.log(`[TabulatorEditManager] Celda: ${field}, Original: "${originalValue}", Nuevo: "${newValue}"`);

      // Usar el método unificado para registrar cambios
      this.registerCellChange(cell, originalValue, newValue);

      // Mostrar iconos de edición
      this.showEditIcons();
  }

  getOriginalValue(cell) {
      const row = cell.getRow();
      const rowData = row.getData();
      
      // CORREGIDO: usar identificador consistente
      const rowId = rowData.ID || rowData.ID_Plantilla;
      const field = cell.getField();

      if (this.originalData.has(rowId)) {
          const originalRow = this.originalData.get(rowId);
          const originalValue = originalRow[field];
          
          // CORREGIDO: retornar valor vacío en lugar de null si no existe
          return originalValue !== undefined ? originalValue : "";
      }

      // CORREGIDO: retornar valor vacío en lugar de null
      console.warn(`[TabulatorEditManager] No se encontraron datos originales para la fila ${rowId}`);
      return "";
  }

  removeFromChanges(cell) {
      const row = cell.getRow();
      const rowData = row.getData();
      
      // CORREGIDO: usar identificador consistente
      const rowId = rowData.ID || rowData.ID_Plantilla;
      const field = cell.getField();

      if (this.changedCells.has(rowId)) {
          delete this.changedCells.get(rowId)[field];

          // Si no quedan cambios en esta fila, eliminar la fila del mapa
          if (Object.keys(this.changedCells.get(rowId)).length === 0) {
              this.changedCells.delete(rowId);
          }
      }

      // Remover feedback visual
      this.removeCellChanged(cell);

      // Ocultar iconos si no hay más cambios
      if (this.changedCells.size === 0) {
          this.hideEditIcons();
      }
  }
  // NUEVO: Registrar un cambio de celda (usado tanto para edición manual como para pegado)
registerCellChange(cell, originalValue, newValue) {
  let row, rowData, rowId, field;
  
  // Verificar si recibimos un objeto cell válido de Tabulator
  if (typeof cell.getRow === 'function') {
      // Es una celda real de Tabulator (edición manual)
      row = cell.getRow();
      rowData = row.getData();
      rowId = rowData.ID || rowData.ID_Plantilla;
      field = cell.getField();
  } else {
      // Es información de pegado, necesitamos extraer los datos de manera diferente
      console.error('[TabulatorEditManager] registerCellChange recibió un objeto cell inválido:', cell);
      return;
  }

  // No hacer nada si el valor no cambió realmente
  if (String(newValue) === String(originalValue)) {
      this.removeFromChanges(cell);
      return;
  }

  // Agregar a cambios pendientes
  if (!this.changedCells.has(rowId)) {
      this.changedCells.set(rowId, {});
  }

  this.changedCells.get(rowId)[field] = {
      oldValue: originalValue,
      newValue: newValue,
      cell: cell,
  };

  // Mostrar feedback visual
  this.showCellChanged(cell);

  console.log(`[TabulatorEditManager] Cambio registrado - Fila: ${rowId}, Campo: ${field}, Original: "${originalValue}", Nuevo: "${newValue}"`);
}

// MÉTODO MEJORADO: Manejar el pegado desde portapapeles
handleClipboardPaste(clipboard, rowData, rows) {
  console.log('[TabulatorEditManager] Procesando pegado de portapapeles...');
  
  rows.forEach(row => {
      const currentRowData = row.getData();
      const rowId = currentRowData.ID || currentRowData.ID_Plantilla;
      
      const originalRowData = this.originalData.get(rowId);
      if (!originalRowData) {
          console.warn(`[TabulatorEditManager] No se encontraron datos originales para la fila ${rowId}`);
          return;
      }

      Object.keys(currentRowData).forEach(field => {
          if (field === 'ID' || field === 'checkbox') return;
          
          const currentValue = currentRowData[field];
          const originalValue = originalRowData[field] !== undefined ? originalRowData[field] : "";
          
          if (String(currentValue) !== String(originalValue)) {
              console.log(`[TabulatorEditManager] Cambio detectado por pegado - Fila: ${rowId}, Campo: ${field}, Original: "${originalValue}", Nuevo: "${currentValue}"`);
              
              const cell = row.getCell(field);
              if (cell) {
                  this.registerPasteChange(rowId, field, cell, originalValue, currentValue);
              }
          }
      });
  });

  if (this.changedCells.size > 0) {
      this.showEditIcons();
  }
}

// NUEVO MÉTODO: Registrar cambios específicamente para pegado
registerPasteChange(rowId, field, cell, originalValue, newValue) {
  // No hacer nada si el valor no cambió realmente
  if (String(newValue) === String(originalValue)) {
      return;
  }

  // Agregar a cambios pendientes
  if (!this.changedCells.has(rowId)) {
      this.changedCells.set(rowId, {});
  }

  this.changedCells.get(rowId)[field] = {
      oldValue: originalValue,
      newValue: newValue,
      cell: cell,
  };

  // Mostrar feedback visual
  this.showCellChanged(cell);

  console.log(`[TabulatorEditManager] Cambio por pegado registrado - Fila: ${rowId}, Campo: ${field}, Original: "${originalValue}", Nuevo: "${newValue}"`);
}


  showCellChanged(cell) {
      // Agregar clase CSS para mostrar que la celda ha cambiado
      const cellElement = cell.getElement();
      cellElement.classList.add("cell-changed");
      cellElement.style.backgroundColor = "#FFFACD"; // Color amarillo claro
  }

  removeCellChanged(cell) {
      const cellElement = cell.getElement();
      cellElement.classList.remove("cell-changed");
      cellElement.style.backgroundColor = "";
  }

  async saveAllChanges() {
      if (!this.puedeEditar) {
          this.showFeedbackMessage(
              "No puedes guardar cambios fuera de las fechas de Programación Académica."
          );
          return;
      }

      if (this.changedCells.size === 0) {
          this.showFeedbackMessage("No hay cambios para guardar.");
          return;
      }

      const totalChanges = Array.from(this.changedCells.values()).reduce(
          (sum, rowChanges) => sum + Object.keys(rowChanges).length,
          0
      );

      if (totalChanges === 0) {
          this.showFeedbackMessage("No hay cambios para guardar.");
          return;
      }

      console.log(`Iniciando guardado de ${totalChanges} cambios...`);

      // Mostrar indicador de carga si existe SweetAlert
      if (typeof Swal !== "undefined") {
          Swal.fire({
              title: "Guardando cambios...",
              text: `Procesando ${totalChanges} cambios`,
              allowOutsideClick: false,
              didOpen: () => {
                  Swal.showLoading();
              },
          });
      }

      const promises = [];

      // Crear promesas para cada cambio
      this.changedCells.forEach((rowChanges, rowId) => {
          Object.entries(rowChanges).forEach(([field, change]) => {
              const columnName = this.columnMap[field] || field;

              promises.push(
                  this.saveCellChange(rowId, columnName, change.newValue)
                      .then((result) => ({
                          rowId,
                          field,
                          change,
                          result,
                          success: true,
                      }))
                      .catch((error) => ({
                          rowId,
                          field,
                          change,
                          error,
                          success: false,
                      }))
              );
          });
      });

      try {
          const results = await Promise.all(promises);
          const successful = results.filter((r) => r.success);
          const failed = results.filter((r) => !r.success);

          console.log(
              `Guardado completado. Exitosos: ${successful.length}, Fallidos: ${failed.length}`
          );

          // Actualizar estado para cambios exitosos
          successful.forEach(({ rowId, field, change }) => {
              // Actualizar datos originales
              if (this.originalData.has(rowId)) {
                  this.originalData.get(rowId)[field] = change.newValue;
              }

              // Mostrar feedback positivo
              this.showCellSaved(change.cell);

              // Remover de cambios pendientes
              if (this.changedCells.has(rowId)) {
                  delete this.changedCells.get(rowId)[field];
                  if (Object.keys(this.changedCells.get(rowId)).length === 0) {
                      this.changedCells.delete(rowId);
                  }
              }
          });

          // Mostrar resultado
          if (failed.length === 0) {
              if (typeof Swal !== "undefined") {
                  Swal.fire({
                      icon: "success",
                      title: "Cambios guardados",
                      text: `Se guardaron ${successful.length} cambios exitosamente.`,
                      timer: 2000,
                      showConfirmButton: false,
                  });
              }

              // Ocultar iconos si todos los cambios se guardaron
              if (this.changedCells.size === 0) {
                  this.hideEditIcons();
              }
          } else {
              // Mostrar errores
              const errorMessages = failed
                  .map((f) => `${f.field}: ${f.error.message || f.error}`)
                  .slice(0, 5);

              if (typeof Swal !== "undefined") {
                  Swal.fire({
                      icon: "warning",
                      title: "Algunos cambios no se pudieron guardar",
                      html: `
                          <p>Exitosos: ${successful.length}</p>
                          <p>Fallidos: ${failed.length}</p>
                          <div style="text-align: left; margin-top: 10px;">
                              <strong>Errores:</strong><br>
                              ${errorMessages.join("<br>")}
                              ${failed.length > 5 ? "<br>... y más" : ""}
                          </div>
                      `,
                      confirmButtonText: "Entendido",
                  });
              }
          }

          // Actualizar notificaciones si la función existe
          if (typeof actualizarNotificaciones === "function") {
              actualizarNotificaciones();
          }
      } catch (error) {
          console.error("Error general al guardar:", error);

          if (typeof Swal !== "undefined") {
              Swal.fire({
                  icon: "error",
                  title: "Error al guardar",
                  text: "Ocurrió un error inesperado al guardar los cambios.",
                  confirmButtonText: "Entendido",
              });
          }
      }
  }

  async saveCellChange(rowId, columnName, value) {
      const response = await fetch(
          "./functions/coord-personal-plantilla/actualizar-celda-coord.php",
          {
              method: "POST",
              headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
              },
              body: new URLSearchParams({
                  id: rowId,
                  column: columnName,
                  value: value,
                  user_role: this.userRole,
              }),
          }
      );

      if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      if (!data.success) {
          throw new Error(data.error || "Error desconocido");
      }

      return data;
  }

  showCellSaved(cell) {
      const cellElement = cell.getElement();
      cellElement.style.backgroundColor = "#90EE90"; // Verde claro

      setTimeout(() => {
          cellElement.style.transition = "background-color 0.5s ease";
          cellElement.style.backgroundColor = "";
          cellElement.classList.remove("cell-changed");

          setTimeout(() => {
              cellElement.style.transition = "";
          }, 500);
      }, 1500);
  }

  undoAllChanges() {
      if (this.changedCells.size === 0) {
          this.showFeedbackMessage("No hay cambios para deshacer.");
          return;
      }

      let changesUndone = 0;

      // Revertir todos los cambios
      this.changedCells.forEach((rowChanges, rowId) => {
          Object.entries(rowChanges).forEach(([field, change]) => {
              // CORREGIDO: verificar que el valor original no sea null/undefined
              if (change.oldValue !== null && change.oldValue !== undefined) {
                  change.cell.setValue(change.oldValue);
              } else {
                  // Si el valor original es null/undefined, usar cadena vacía
                  change.cell.setValue("");
              }

              // Remover feedback visual
              this.removeCellChanged(change.cell);

              changesUndone++;
          });
      });

      // Limpiar cambios pendientes
      this.changedCells.clear();

      // Ocultar iconos
      this.hideEditIcons();

      console.log(`Se deshicieron ${changesUndone} cambios`);

      this.showFeedbackMessage(`Se deshicieron ${changesUndone} cambios.`);
  }

  setupIconEvents() {
      // Evento para guardar
      const saveIcon = document.getElementById("icono-guardar");
      if (saveIcon) {
          saveIcon.addEventListener("click", (e) => {
              e.preventDefault();
              this.saveAllChanges();
          });
      }

      // Evento para deshacer
      const undoIcon = document.getElementById("icono-deshacer");
      if (undoIcon) {
          undoIcon.addEventListener("click", (e) => {
              e.preventDefault();
              this.undoAllChanges();
          });
      }
  }

  showEditIcons() {
      console.log('[TabulatorEditManager] Mostrando iconos...');
      const saveIcon = document.getElementById("icono-guardar");
      const undoIcon = document.getElementById("icono-deshacer");
      
      if (saveIcon) {
          saveIcon.style.opacity = '1';
          saveIcon.style.visibility = 'visible';
      }
      if (undoIcon) {
          undoIcon.style.opacity = '1';
          undoIcon.style.visibility = 'visible';
      }
  }
  
  hideEditIcons() {
      console.log('[TabulatorEditManager] Ocultando iconos...');
      const saveIcon = document.getElementById("icono-guardar");
      const undoIcon = document.getElementById("icono-deshacer");
      
      if (saveIcon) {
          saveIcon.style.opacity = '0';
          saveIcon.style.visibility = 'hidden';
      }
      if (undoIcon) {
          undoIcon.style.opacity = '0';
          undoIcon.style.visibility = 'hidden';
      }
  }

  showFeedbackMessage(message) {
      if (typeof Swal !== "undefined") {
          Swal.fire({
              text: message,
              timer: 2000,
              showConfirmButton: false,
              position: "top-end",
              toast: true,
          });
      } else {
          console.log(message);
          // Fallback con alert si no hay SweetAlert
          // alert(message);
      }
  }

  // Método público para obtener estadísticas
  getChangeStats() {
      const totalRows = this.changedCells.size;
      const totalChanges = Array.from(this.changedCells.values()).reduce(
          (sum, rowChanges) => sum + Object.keys(rowChanges).length,
          0
      );

      return {
          totalRows,
          totalChanges,
          hasChanges: totalChanges > 0,
      };
  }
}

// Funciones globales para mantener compatibilidad
function saveAllChanges() {
  if (window.editManager) {
      window.editManager.saveAllChanges();
  }
}

function undoAllChanges() {
  if (window.editManager) {
      window.editManager.undoAllChanges();
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
  // Esperar un poco para asegurar que Tabulator esté inicializado
  setTimeout(() => {
      window.editManager = new TabulatorEditManager();

      // Agregar estilos CSS necesarios
      const style = document.createElement("style");
      style.textContent = `
          .cell-changed {
              background-color: #FFFACD !important;
              position: relative;
          }
          
          .cell-changed::after {
              content: '';
              position: absolute;
              top: 0;
              right: 0;
              width: 0;
              height: 0;
              border-left: 8px solid transparent;
              border-top: 8px solid #ff6b6b;
              pointer-events: none;
          }
          
          #icono-guardar, #icono-deshacer {
              transition: visibility 0.3s, opacity 0.3s;
          }
      `;
      document.head.appendChild(style);
  }, 500);
});

// Exportar la clase para uso global
window.TabulatorEditManager = TabulatorEditManager;