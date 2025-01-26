function mostrarFormularioAñadir() {
  document.getElementById("modal-añadir").style.display = "block";
}

function cerrarFormularioAñadir() {
  document.getElementById("modal-añadir").style.display = "none";
}

// Cerrar el modal al hacer clic en la X
document.querySelector(".close").onclick = function () {
  cerrarFormularioAñadir();
};

// Cerrar el modal al hacer clic fuera de él
window.onclick = function (event) {
  if (event.target == document.getElementById("modal-añadir")) {
    cerrarFormularioAñadir();
  }
};

function añadirRegistro() {
  var form = document.getElementById("form-añadir-registro");
  var datos = new FormData(form);

  var departamento_id = document.getElementById("departamento_id").value;
  datos.append("departamento_id", departamento_id);

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./functions/basesdedatos/añadir-registro.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        try {
          var respuesta = JSON.parse(xhr.responseText);
          if (respuesta.success) {
            Swal.fire({
              title: "¡Éxito!",
              text: respuesta.message,
              icon: "success",
            }).then(() => {
              cerrarFormularioAñadir();
              location.reload();
            });
          } else {
            Swal.fire({
              title: "Error",
              text: respuesta.message,
              icon: "error",
            });
          }
        } catch (e) {
          Swal.fire({
            title: "Error",
            text: "Error al procesar la respuesta del servidor",
            icon: "error",
          });
        }
      } else {
        Swal.fire({
          title: "Error",
          text: "Error de conexión con el servidor",
          icon: "error",
        });
      }
    }
  };
  xhr.send(datos);
}

// Condicionales y estilos del modal.
// --------------------------------- //
var lunes = document.getElementById('l');
var martes = document.getElementById('m');
var miercoles = document.getElementById('i');
var jueves = document.getElementById('j');
var viernes = document.getElementById('v');
var sabado = document.getElementById('s');
var domingo = document.getElementById('d');

// Funcion para manejar la modalidad seleccionada
document.getElementById('modalidad').addEventListener('change', function() {
    const modalidad = this.value;
    if (modalidad === 'PRESENCIAL ENRIQUECIDA') {
            // Para que aparezca el contenedor de dias presenciales y virtuales
        document.getElementById('presencial-virtual').style.display = 'flex';
            // Titulos e inputs para dias presenciales y ocultar los virtuales
        document.getElementById('title_dia_presencial').style.display = 'block';
        document.getElementById('dia_presencial').style.display = 'block';
        document.getElementById('title_dia_virtual').style.display = 'none';
        document.getElementById('dia_virtual').style.display = 'none';
            // Ocultar las opciones de desplegables de dias mixtos. 
        document.getElementById('mixta').style.display = 'none';
        document.getElementById('dia_presencial2').style.display = 'none';
        document.getElementById('dia_virtual2').style.display = 'none';
    } else if (modalidad === 'VIRTUAL') {
            // Para que aparezca el contenedor de dias presenciales y virtuales
        document.getElementById('presencial-virtual').style.display = 'flex';
            // Titulos e inputs para dias virtuales y ocultar los presenciales
        document.getElementById('title_dia_virtual').style.display = 'block';
        document.getElementById('dia_virtual').style.display = 'block';
        document.getElementById('title_dia_presencial').style.display = 'none';
        document.getElementById('dia_presencial').style.display = 'none';
            // Ocultar las opciones de desplegables de dias mixtos. 
        document.getElementById('mixta').style.display = 'none';
        document.getElementById('dia_presencial2').style.display = 'none';
        document.getElementById('dia_virtual2').style.display = 'none';
    } else if (modalidad === 'MIXTA') {
            // Para que aparezca el contenedor de dias mixtos
        document.getElementById('mixta').style.display = 'flex';
            // Titulos y <select> para dias presenciales y virtuales
        document.getElementById('title_dia_presencial').style.display = 'block';
        document.getElementById('dia_presencial2').style.display = 'block';
        document.getElementById('title_dia_virtual').style.display = 'block';
        document.getElementById('dia_virtual2').style.display = 'block';
            // Ocultar inputs de presenciales y virtuales
        document.getElementById('dia_presencial').style.display = 'none';
        document.getElementById('dia_virtual').style.display = 'none';
    }

    function actualizarDia() {
        var diaPresencial = '';
        var diaVirtual = '';
    
        if(modalidad === 'PRESENCIAL ENRIQUECIDA' || modalidad === 'VIRTUAL') {
            if (lunes.value === 'L') {
                diaPresencial = 'LUNES';
                diaVirtual = 'LUNES';
            }
            if (martes.value === 'M') {
                diaPresencial = 'MARTES';
                diaVirtual = 'MARTES';
            }
            if (miercoles.value === 'I') {
                diaPresencial = 'MIERCOLES';
                diaVirtual = 'MIERCOLES';
            }
            if (jueves.value === 'J') {
                diaPresencial = 'JUEVES';
                diaVirtual = 'JUEVES';
            }
            if (viernes.value === 'V') {
                diaPresencial = 'VIERNES';
                diaVirtual = 'VIERNES';
            }
            if (sabado.value === 'S') {
                diaPresencial = 'SABADO';
                diaVirtual = 'SABADO';
            }
            if (domingo.value === 'D') {
                diaPresencial = 'DOMINGO';
                diaVirtual = 'DOMINGO';
            }
        }

        if(modalidad === 'MIXTA') {
        const diaPresencial2 = document.getElementById('dia_presencial2').value;
        const diaVirtual2 = document.getElementById('dia_virtual2').value;
            if (lunes.value === 'L') {
                document.getElementById('lun').style.display = 'block';
                document.getElementById('lun2').style.display = 'block';
            } else {
                document.getElementById('lun').style.display = 'none';
                document.getElementById('lun2').style.display = 'none';
            }
            if (martes.value === 'M') {
                document.getElementById('mar').style.display = 'block';
                document.getElementById('mar2').style.display = 'block';
            } else {
                document.getElementById('mar').style.display = 'none';
                document.getElementById('mar2').style.display = 'none';
            }
            if (miercoles.value === 'I') {
                document.getElementById('mie').style.display = 'block';
                document.getElementById('mie2').style.display = 'block';
            } else {
                document.getElementById('mie').style.display = 'none';
                document.getElementById('mie2').style.display = 'none';
            }
            if (jueves.value === 'J') {
                document.getElementById('jue').style.display = 'block';
                document.getElementById('jue2').style.display = 'block';
            } else {
                document.getElementById('jue').style.display = 'none';
                document.getElementById('jue2').style.display = 'none';
            }
            if (viernes.value === 'V') {
                document.getElementById('vie').style.display = 'block';
                document.getElementById('vie2').style.display = 'block';
            } else {
                document.getElementById('vie').style.display = 'none';
                document.getElementById('vie2').style.display = 'none';
            }
            if (sabado.value === 'S') {
                document.getElementById('sab').style.display = 'block';
                document.getElementById('sab2').style.display = 'block';
            } else {
                document.getElementById('sab').style.display = 'none';
                document.getElementById('sab2').style.display = 'none';
            }
            if (domingo.value === 'D') {
                document.getElementById('dom').style.display = 'block';
                document.getElementById('dom2').style.display = 'block';
            } else {
                document.getElementById('dom').style.display = 'none';
                document.getElementById('dom2').style.display = 'none';
            }
            diaPresencial = diaPresencial2;
            diaVirtual = diaVirtual2;
        }
    
        var totalDias = 0;
        totalDias += lunes.value === 'L' ? 1 : 0;
        totalDias += martes.value === 'M' ? 1 : 0;
        totalDias += miercoles.value === 'I' ? 1 : 0;
        totalDias += jueves.value === 'J' ? 1 : 0;
        totalDias += viernes.value === 'V' ? 1 : 0;
        totalDias += sabado.value === 'S' ? 1 : 0;
        totalDias += domingo.value === 'D' ? 1 : 0;
    
        if ((totalDias > 1) && (modalidad === 'PRESENCIAL ENRIQUECIDA' || modalidad === 'VIRTUAL')) {
            document.getElementById('dia_presencial').value = 'AMBOS';
            document.getElementById('dia_virtual').value = 'AMBOS';
        } else if (diaPresencial && diaVirtual) {
            document.getElementById('dia_presencial').value = diaPresencial;
            document.getElementById('dia_virtual').value = diaVirtual;
        } else {
            document.getElementById('dia_presencial').value = '';
            document.getElementById('dia_virtual').value = '';  
        }
    }

  // Agregar event listeners a cada campo de texto de los dias (L, M, I, J, V, S, D)
  lunes.addEventListener("input", actualizarDia);
  martes.addEventListener("input", actualizarDia);
  miercoles.addEventListener("input", actualizarDia);
  jueves.addEventListener("input", actualizarDia);
  viernes.addEventListener("input", actualizarDia);
  sabado.addEventListener("input", actualizarDia);
  domingo.addEventListener("input", actualizarDia);
});

// Variables para cada ID de <select>
var nivel = document.getElementById('nivel');
var tipo = document.getElementById('tipo');
var nivel_tipo = document.getElementById('nivel_tipo');
var estatus = document.getElementById('estatus');
var modalidad_option = document.getElementById('modalidad');
var dia_presencial_option = document.getElementById('dia_presencial2');
var dia_virtual_option = document.getElementById('dia_virtual2');
var examen_extraordinario = document.getElementById('examen_extraordinario');
var tipo_contrato = document.getElementById('tipo_contrato');
var categoria = document.getElementById('categoria');
var descarga = document.getElementById('descarga');
var titular = document.getElementById('titular');
var hora_inicial = document.getElementById('hora_inicial');
var hora_final = document.getElementById('hora_final');

// Cambiar el color de texto para <select> al estar activo
nivel.addEventListener('change', function() {
    if (nivel.value !== 'Seleccione el nivel correspondiente...') {
        nivel.style.color = '#000000'; 
        nivel.style.fontStyle = 'normal';
    } else {
        nivel.style.color = '';  
        nivel.style.fontStyle = '';
    }
});
tipo.addEventListener('change', function() {
    if (tipo.value !== 'Seleccione la opción correspondiente...') {
        tipo.style.color = '#000000'; 
        tipo.style.fontStyle = 'normal';
    } else {
        tipo.style.color = '';  
        tipo.style.fontStyle = '';
    }
});
nivel_tipo.addEventListener('change', function() {
    if (nivel_tipo.value !== 'Seleccione la opción correspondiente...') {
        nivel_tipo.style.color = '#000000'; 
        nivel_tipo.style.fontStyle = 'normal';
    } else {
        nivel_tipo.style.color = '';  
        nivel_tipo.style.fontStyle = '';
    }
});
estatus.addEventListener('change', function() {
    if (estatus.value !== 'Seleccione la opción correspondiente...') {
        estatus.style.color = '#000000'; 
        estatus.style.fontStyle = 'normal';
    } else {
        estatus.style.color = '';  
        estatus.style.fontStyle = '';
    }
});
modalidad_option.addEventListener('change', function() {
    if (modalidad_option.value !== 'Seleccione la modalidad correspondiente...') {
        modalidad_option.style.color = '#000000'; 
        modalidad_option.style.fontStyle = 'normal';
    } else {
        modalidad_option.style.color = '';  
        modalidad_option.style.fontStyle = '';
    }
});
dia_presencial_option.addEventListener('change', function() {
    if (dia_presencial_option.value !== 'Seleccione el dia presencial...') {
        dia_presencial_option.style.color = '#000000'; 
        dia_presencial_option.style.fontStyle = 'normal';
    } else {
        dia_presencial_option.style.color = '';  
        dia_presencial_option.style.fontStyle = '';
    }
});
dia_virtual_option.addEventListener('change', function() {
    if (dia_virtual_option.value !== 'Seleccione el dia virtual...') {
        dia_virtual_option.style.color = '#000000'; 
        dia_virtual_option.style.fontStyle = 'normal';
    } else {
        dia_virtual_option.style.color = '';  
        dia_virtual_option.style.fontStyle = '';
    }
});
examen_extraordinario.addEventListener('change', function() {
    if (examen_extraordinario.value !== 'Seleccione la opción correspondiente...') {
        examen_extraordinario.style.color = '#000000'; 
        examen_extraordinario.style.fontStyle = 'normal';
    } else {
        examen_extraordinario.style.color = '';  
        examen_extraordinario.style.fontStyle = '';
    }
});
tipo_contrato.addEventListener('change', function() {
    if (tipo_contrato.value !== 'Seleccione el tipo de contrato correspondiente...') {
        tipo_contrato.style.color = '#000000'; 
        tipo_contrato.style.fontStyle = 'normal';
    } else {
        tipo_contrato.style.color = '';  
        tipo_contrato.style.fontStyle = '';
    }
});
categoria.addEventListener('change', function() {
    if (categoria.value !== 'Seleccione la categoria correspondiente...') {
        categoria.style.color = '#000000'; 
        categoria.style.fontStyle = 'normal';
    } else {
        categoria.style.color = '';  
        categoria.style.fontStyle = '';
    }
});
descarga.addEventListener('change', function() {
    if (descarga.value !== 'Seleccione la opción correspondiente...') {
        descarga.style.color = '#000000'; 
        descarga.style.fontStyle = 'normal';
    } else {
        descarga.style.color = '';  
        descarga.style.fontStyle = '';
    }
});
titular.addEventListener('change', function() {
    if (titular.value !== 'Seleccione la opción correspondiente...') {
        titular.style.color = '#000000'; 
        titular.style.fontStyle = 'normal';
    } else {
        titular.style.color = '';  
        titular.style.fontStyle = '';
    }
});
hora_inicial.addEventListener('change', function() {
    if (hora_inicial.value !== '') {
        hora_inicial.style.color = '#000000'; 
        hora_inicial.style.fontStyle = 'normal';
    } else {
        hora_inicial.style.color = '';  
        hora_inicial.style.fontStyle = '';
    }
});

hora_final.addEventListener('change', function() {
    if (hora_final.value !== '') {
        hora_final.style.color = '#000000'; 
        hora_final.style.fontStyle = 'normal';
    } else {
        hora_final.style.color = '';  
        hora_final.style.fontStyle = '';
    }
});