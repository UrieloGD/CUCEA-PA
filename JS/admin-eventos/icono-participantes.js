// Obtener color aleatorio
function getRandomColor() {
  const letters = "56789ABC";
  let color = "#";
  for (let i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * letters.length)];
  }
  return color;
}

// Seleccionar todos los iconos de perfil
const iconosPerfil = document.querySelectorAll(".icono-perfil");

// Asignar un color aleatorio a cada icono individualmente
iconosPerfil.forEach(icono => {
  const colorPerfil = getRandomColor(); 
  icono.style.backgroundColor = colorPerfil;
});