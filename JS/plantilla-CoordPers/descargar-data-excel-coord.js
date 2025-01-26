function mostrarDescargarExcel() {
  document.getElementById('modal-descargar').style.display = 'block';
}

function cerrarDescargarExcel() {
  document.getElementById('modal-descargar').style.display = 'none';
}

// Inicializar los manejadores de checkbox
function initializeCheckboxHandlers() {
  // Manejador para "Seleccionar/Deseleccionar todas"
  const selectAllMain = document.querySelector('.select-first input[type="checkbox"]');
  if (selectAllMain) {
    selectAllMain.addEventListener('change', function() {
      const allCheckboxes = document.querySelectorAll('.columns-container-basica input[type="checkbox"], .columns-container-academica input[type="checkbox"],' +
         ' .columns-container-personal input[type="checkbox"], .columns-container-academica2 input[type="checkbox"], .columns-container-profesores input[type="checkbox"],' + 
        ' .columns-container-antiguedad input[type="checkbox"]');
      allCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      
      // También actualiza los checkboxes de grupo
      const selectAllBasica = document.querySelector('.select-all-basica input[type="checkbox"]');
      const selectAllAcademica = document.querySelector('.select-all-academica input[type="checkbox"]');
      const selectAllPersonal = document.querySelector('.select-all-personal input[type="checkbox"]');
      const selectAllAcademica2 = document.querySelector('.select-all-academica2 input[type="checkbox"]');
      const selectAllProfesores = document.querySelector('.select-all-profesores input[type="checkbox"]');
      const selectAllAntiguedad = document.querySelector('.select-all-antiguedad input[type="checkbox"]');
      if (selectAllBasica) selectAllBasica.checked = this.checked;
      if (selectAllAcademica) selectAllAcademica.checked = this.checked;
      if (selectAllPersonal) selectAllPersonal.checked = this.checked;
      if (selectAllAcademica2) selectAllAcademica2.checked = this.checked;
      if (selectAllProfesores) selectAllProfesores.checked = this.checked;
      if (selectAllAntiguedad) selectAllAntiguedad.checked = this.checked;
    });
  }

  // Manejador para "Seleccionar/Deseleccionar grupo completo de Información básica"
  const selectAllBasica = document.querySelector('.select-all-basica input[type="checkbox"]');
  if (selectAllBasica) {
    selectAllBasica.addEventListener('change', function() {
      const basicaCheckboxes = document.querySelectorAll('.columns-container-basica input[type="checkbox"]');
      basicaCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateMainCheckbox();
    });
  }

  // Manejador para "Seleccionar/Deseleccionar grupo completo de Información académica"
  const selectAllAcademica = document.querySelector('.select-all-academica input[type="checkbox"]');
  if (selectAllAcademica) {
    selectAllAcademica.addEventListener('change', function() {
      const academicaCheckboxes = document.querySelectorAll('.columns-container-academica input[type="checkbox"]');
      academicaCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateMainCheckbox();
    });
  }

  // Manejador para "Seleccionar/Deseleccionar grupo completo de Información personal"
  const selectAllPersonal = document.querySelector('.select-all-personal input[type="checkbox"]');
  if (selectAllPersonal) {
    selectAllPersonal.addEventListener('change', function() {
      const personalCheckboxes = document.querySelectorAll('.columns-container-personal input[type="checkbox"]');
      personalCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateMainCheckbox();
    });
  }

  // Manejador para "Seleccionar/Deseleccionar grupo completo de Formación académica"
  const selectAllAcademica2 = document.querySelector('.select-all-academica2 input[type="checkbox"]');
  if (selectAllAcademica2) {
    selectAllAcademica2.addEventListener('change', function() {
      const academica2Checkboxes = document.querySelectorAll('.columns-container-academica2 input[type="checkbox"]');
      academica2Checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateMainCheckbox();
    });
  }

  // Manejador para "Seleccionar/Deseleccionar grupo completo de Profesores 24-25"
  const selectAllProfesores = document.querySelector('.select-all-profesores input[type="checkbox"]');
  if (selectAllProfesores) {
    selectAllProfesores.addEventListener('change', function() {
      const profesoresCheckboxes = document.querySelectorAll('.columns-container-profesores input[type="checkbox"]');
      profesoresCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateMainCheckbox();
    });
  }

  // Manejador para "Seleccionar/Deseleccionar grupo completo de Antiguedad"
  const selectAllAntiguedad = document.querySelector('.select-all-antiguedad input[type="checkbox"]');
  if (selectAllAntiguedad) {
    selectAllAntiguedad.addEventListener('change', function() {
      const antiguedadCheckboxes = document.querySelectorAll('.columns-container-antiguedad input[type="checkbox"]');
      antiguedadCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateMainCheckbox();
    });
  }

  // Agregar eventos change a todos los checkboxes individuales
  const allIndividualCheckboxes = document.querySelectorAll('.columns-container-basica input[type="checkbox"], .columns-container-academica input[type="checkbox"],' +
         ' .columns-container-personal input[type="checkbox"], .columns-container-academica2 input[type="checkbox"], .columns-container-profesores input[type="checkbox"],' + 
        ' .columns-container-antiguedad input[type="checkbox"]');
  allIndividualCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      updateGroupCheckboxes();
      updateMainCheckbox();
    });
  });
}

// Actualizar el estado de los checkboxes de grupo
function updateGroupCheckboxes() {
  // Verificar grupo de Información Básica
  const basicaCheckboxes = Array.from(document.querySelectorAll('.columns-container-basica input[type="checkbox"]'));
  const basicaAllChecked = basicaCheckboxes.length > 0 && basicaCheckboxes.every(cb => cb.checked);
  const basicaSomeChecked = materiaCheckboxes.some(cb => cb.checked);
  
  const selectAllBasica = document.querySelector('.select-all-basica input[type="checkbox"]');
  if (selectAllBasica) {
    selectAllBasica.checked = basicaAllChecked;
    selectAllBasica.indeterminate = !basicaAllChecked && basicaSomeChecked;
  }

  // Verificar grupo de Información Académica
  const academicaCheckboxes = Array.from(document.querySelectorAll('.columns-container-academica input[type="checkbox"]'));
  const academicaAllChecked = academicaCheckboxes.length > 0 && academicaCheckboxes.every(cb => cb.checked);
  const academicaSomeChecked = academicaCheckboxes.some(cb => cb.checked);
  
  const selectAllAcademica = document.querySelector('.select-all-academica input[type="checkbox"]');
  if (selectAllAcademica) {
    selectAllAcademica.checked = academicaAllChecked;
    selectAllAcademica.indeterminate = !academicaAllChecked && academicaSomeChecked;
  }

  // Verificar grupo de Información Personal
  const personalCheckboxes = Array.from(document.querySelectorAll('.columns-container-personal input[type="checkbox"]'));
  const personalAllChecked = personalCheckboxes.length > 0 && personalCheckboxes.every(cb => cb.checked);
  const personalSomeChecked = personalCheckboxes.some(cb => cb.checked);
  
  const selectAllPersonal = document.querySelector('.select-all-personal input[type="checkbox"]');
  if (selectAllPersonal) {
    selectAllPersonal.checked = personalAllChecked;
    selectAllPersonal.indeterminate = !personalAllChecked && personalSomeChecked;
  }

  // Verificar grupo de Formación Académica
  const academica2Checkboxes = Array.from(document.querySelectorAll('.columns-container-academica2 input[type="checkbox"]'));
  const academica2AllChecked = academica2Checkboxes.length > 0 && academica2Checkboxes.every(cb => cb.checked);
  const academica2SomeChecked = academica2Checkboxes.some(cb => cb.checked);
  
  const selectAllAcademica2 = document.querySelector('.select-all-academica2 input[type="checkbox"]');
  if (selectAllAcademica2) {
    selectAllAcademica2.checked = academica2AllChecked;
    selectAllAcademica2.indeterminate = !academica2AllChecked && academica2SomeChecked;
  }

  // Verificar grupo de Profesores 24-25
  const profesoresCheckboxes = Array.from(document.querySelectorAll('.columns-container-profesores input[type="checkbox"]'));
  const profesoresAllChecked = profesoresCheckboxes.length > 0 && profesoresCheckboxes.every(cb => cb.checked);
  const profesoresSomeChecked = profesoresCheckboxes.some(cb => cb.checked);
  
  const selectAllProfesores = document.querySelector('.select-all-profesores input[type="checkbox"]');
  if (selectAllProfesores) {
    selectAllProfesores.checked = profesoresAllChecked;
    selectAllProfesores.indeterminate = !profesoresAllChecked && profesoresSomeChecked;
  }

  // Verificar grupo de Antiguedad
  const antiguedadCheckboxes = Array.from(document.querySelectorAll('.columns-container-antiguedad input[type="checkbox"]'));
  const antiguedadAllChecked = antiguedadCheckboxes.length > 0 && antiguedadCheckboxes.every(cb => cb.checked);
  const antiguedadSomeChecked = antiguedadCheckboxes.some(cb => cb.checked);
  
  const selectAllAntiguedad = document.querySelector('.select-all-antiguedad input[type="checkbox"]');
  if (selectAllAntiguedad) {
    selectAllAntiguedad.checked = antiguedadAllChecked;
    selectAllAntiguedad.indeterminate = !antiguedadAllChecked && antiguedadSomeChecked;
  }

}

// Actualizar el estado del checkbox principal
function updateMainCheckbox() {
  const allCheckboxes = Array.from(document.querySelectorAll('.columns-container-basica input[type="checkbox"], .columns-container-academica input[type="checkbox"],' +
    ' .columns-container-personal input[type="checkbox"], .columns-container-academica2 input[type="checkbox"], .columns-container-profesores input[type="checkbox"],' + 
   ' .columns-container-antiguedad input[type="checkbox"]'));
  const allChecked = allCheckboxes.length > 0 && allCheckboxes.every(cb => cb.checked);
  const someChecked = allCheckboxes.some(cb => cb.checked);
  
  const mainCheckbox = document.querySelector('.select-first input[type="checkbox"]');
  if (mainCheckbox) {
    mainCheckbox.checked = allChecked;
    mainCheckbox.indeterminate = !allChecked && someChecked;
  }
}

function descargarExcelSeleccionado() {
  // Obtener todas las columnas seleccionadas de ambos contenedores
  var columnasSeleccionadas = Array.from(
    document.querySelectorAll(
      '.columns-container-basica input[type="checkbox"]:checked, .columns-container-academica input[type="checkbox"]:checked,' +
      ' .columns-container-personal input[type="checkbox"]:checked, .columns-container-academica2 input[type="checkbox"]:checked,' + 
      ' .columns-container-profesores input[type="checkbox"]:checked, .columns-container-antiguedad input[type="checkbox"]:checked'
    )
  ).map((checkbox) => checkbox.value);

  if (columnasSeleccionadas.length === 0) {
    alert("Por favor, selecciona al menos una columna.");
    return;
  }

  var url =
    "./functions/coord-personal-plantilla/descargar-data-excel-coord.php?columnas=" +
    JSON.stringify(columnasSeleccionadas);

  window.location.href = url;
  cerrarDescargarExcel();
}

// Cerrar el modal al hacer clic en la X
document.querySelector('.close').onclick = function() {
  cerrarDescargarExcel();
}

// Cerrar el modal al hacer clic fuera de él
window.onclick = function(event) {
  if (event.target == document.getElementById('modal-descargar')) {
      cerrarDescargarExcel();
  }
}

// Inicializar los handlers cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  initializeCheckboxHandlers();
});