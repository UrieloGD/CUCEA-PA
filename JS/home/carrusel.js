const carrusel = document.querySelector('.carrusel');
const diapositivas = document.querySelectorAll('.diapositiva');
const puntos = document.querySelectorAll('.punto');
const botonAnterior = document.querySelector('#botonAnterior');
const botonSiguiente = document.querySelector('#botonSiguiente');
        
let diapositivaActual = 0;
const totalDiapositivas = diapositivas.length;

function actualizarCarrusel() {
  carrusel.style.transform = `translateX(-${diapositivaActual * 100}%)`;
  puntos.forEach((punto, index) => {
    punto.classList.toggle('activo', index === diapositivaActual);
  });
}

function siguienteDiapositiva() {
  diapositivaActual = (diapositivaActual + 1) % totalDiapositivas;
  actualizarCarrusel();
}

function diapositivaAnterior() {
  diapositivaActual = (diapositivaActual - 1 + totalDiapositivas) % totalDiapositivas;
  actualizarCarrusel();
}

botonSiguiente.addEventListener('click', siguienteDiapositiva);
botonAnterior.addEventListener('click', diapositivaAnterior);
        
puntos.forEach((punto, index) => {
  punto.addEventListener('click', () => {
    diapositivaActual = index;
    actualizarCarrusel();
  });
});

// Autoplay
setInterval(siguienteDiapositiva, 5000);