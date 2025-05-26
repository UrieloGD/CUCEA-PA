// tabulator-edit-manager.js
class TabulatorEditManager {
  constructor() {
    this.changedCells = new Map(); // Almacena cambios pendientes {rowId: {column: {oldValue, newValue}}}
    this.originalData = new Map(); // Backup de datos originales
    this.table = null;
    this.userRole = null;
    this.departmentId = null;
    this.puedeEditar = window.puedeEditar || true;

    // Mapeo de columnas para la base de datos
    this.columnMap = {
      ID: "ID_Plantilla",
      CICLO: "CICLO",
      CRN: "CRN",
      MATERIA: "MATERIA",
      "CVE MATERIA": "CVE_MATERIA",
      SECCIÓN: "SECCION",
      NIVEL: "NIVEL",
      "NIVEL TIPO": "NIVEL_TIPO",
      TIPO: "TIPO",
      "C. MIN": "C_MIN",
      "H. TOTALES": "H_TOTALES",
      STATUS: "ESTATUS",
      "TIPO CONTRATO": "TIPO_CONTRATO",
      CÓDIGO: "CODIGO_PROFESOR",
      "NOMBRE PROFESOR": "NOMBRE_PROFESOR",
      CATEGORIA: "CATEGORIA",
      DESCARGA: "DESCARGA",
      "CÓDIGO DESCARGA": "CODIGO_DESCARGA",
      "NOMBRE DESCARGA": "NOMBRE_DESCARGA",
      "NOMBRE DEFINITIVO": "NOMBRE_DEFINITIVO",
      TITULAR: "TITULAR",
      HORAS: "HORAS",
      "CÓDIGO DEPENDENCIA": "CODIGO_DEPENDENCIA",
      L: "L",
      M: "M",
      I: "I",
      J: "J",
      V: "V",
      S: "S",
      D: "D",
      "DÍA PRESENCIAL": "DIA_PRESENCIAL",
      "DÍA VIRTUAL": "DIA_VIRTUAL",
      MODALIDAD: "MODALIDAD",
      "FECHA INICIAL": "FECHA_INICIAL",
      "FECHA FINAL": "FECHA_FINAL",
      "HORA INICIAL": "HORA_INICIAL",
      "HORA FINAL": "HORA_FINAL",
      MÓDULO: "MODULO",
      AULA: "AULA",
      CUPO: "CUPO",
      OBSERVACIONES: "OBSERVACIONES",
      EXTRAORDINARIO: "EXAMEN_EXTRAORDINARIO",
    };

    this.init();
  }

  init() {
    // Obtener elementos del DOM
    this.userRole =
      document.getElementById("user-role")?.value ||
      document
        .getElementById("departamento_id")
        ?.getAttribute("data-user-role");
    this.departmentId = document.getElementById("departamento_id")?.value;

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
      } else {
        setTimeout(checkTable, 100);
      }
    };
    checkTable();
  }

  setupTableEvents() {
    if (!this.table) return;

    // Evento cuando se edita una celda
    this.table.on("cellEdited", (cell) => {
      if (!this.puedeEditar) {
        this.showFeedbackMessage(
          "No puedes editar fuera de las fechas de Programación Académica."
        );
        // Revertir el cambio
        const originalValue = this.getOriginalValue(cell);
        if (originalValue !== null) {
          cell.setValue(originalValue);
        }
        return;
      }

      this.handleCellEdit(cell);
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

  backupOriginalData() {
    if (!this.table) return;

    // Respaldar todos los datos originales
    this.table.getData().forEach((row) => {
      this.originalData.set(row.ID_Plantilla, JSON.parse(JSON.stringify(row)));
    });
  }

  handleCellEdit(cell) {
    const row = cell.getRow();
    const rowId = row.getData().ID_Plantilla;
    const field = cell.getField();
    const newValue = cell.getValue();
    const originalValue = this.getOriginalValue(cell);

    // No hacer nada si el valor no cambió
    if (newValue === originalValue) {
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

    // Mostrar iconos de edición
    this.showEditIcons();

    console.log(
      `Cell edited - Row: ${rowId}, Field: ${field}, Old: ${originalValue}, New: ${newValue}`
    );
  }

  getOriginalValue(cell) {
    const row = cell.getRow();
    const rowId = row.getData().ID_Plantilla;
    const field = cell.getField();

    if (this.originalData.has(rowId)) {
      return this.originalData.get(rowId)[field];
    }

    return null;
  }

  removeFromChanges(cell) {
    const row = cell.getRow();
    const rowId = row.getData().ID_Plantilla;
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
      "./functions/basesdedatos/actualizar-celda.php",
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
          department_id: this.departmentId,
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
      this.showFeedbackMessage("Cambios deshechos.");
      return;
    }

    let changesUndone = 0;

    // Revertir todos los cambios
    this.changedCells.forEach((rowChanges, rowId) => {
      Object.entries(rowChanges).forEach(([field, change]) => {
        // Revertir el valor en la tabla
        change.cell.setValue(change.oldValue);

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
    const saveIcon = document.getElementById("icono-guardar");
    const undoIcon = document.getElementById("icono-deshacer");

    if (saveIcon) {
      saveIcon.style.visibility = "visible";
      saveIcon.style.opacity = "1";
    }

    if (undoIcon) {
      undoIcon.style.visibility = "visible";
      undoIcon.style.opacity = "1";
    }
  }

  hideEditIcons() {
    const saveIcon = document.getElementById("icono-guardar");
    const undoIcon = document.getElementById("icono-deshacer");

    if (saveIcon) {
      saveIcon.style.visibility = "hidden";
      saveIcon.style.opacity = "0";
    }

    if (undoIcon) {
      undoIcon.style.visibility = "hidden";
      undoIcon.style.opacity = "0";
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
