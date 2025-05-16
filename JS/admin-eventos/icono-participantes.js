// Obtener color aleatorio
function getRandomColor() {
  const letters = "56789ABC";
  let color = "#";
  for (let i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * letters.length)];
  }
  return color;
}

document.addEventListener("DOMContentLoaded", () => {
    const coloresFijos = {
      'JA': '#B00F0F',
      'JP': '#4C4CC2',
      'BS': '#B75CFF',
      'TT': '#4C4CC2',
      'JC': '#B75CFF',
      'MR': '#F46BBD',
      'SR': '#DF2E79',
      'GS': '#DF2E79',
      'CA': '#064789',
      'CM': '#F46BBD',
      'AC': '#F46BBD',
      'JS': '#064789',
      'CA': '#4C4CC2',
      'AL': '#03CD54',
      'CF': '#03CD54',
      'JR': '#B75CFF',
      'ML': '#B75CFF',
      'DM': '#064789',
      'AC': '#B00F0F',
      'SG': '#FF6F32',
      'LG': '#F46BBD',
      'DS': '#064789',
      'IA': '#B00F0F'
    };

    document.querySelectorAll('.icono-perfil').forEach(icono => {
      const iniciales = icono.dataset.usuario;
      const color = coloresFijos[iniciales] || '#999'; // por defecto si no está definido
      icono.style.backgroundColor = color;
    });
});