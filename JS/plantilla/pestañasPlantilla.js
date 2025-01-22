const tabButtons = document.querySelectorAll(".tab-button");
const tabPanes = document.querySelectorAll(".tab-pane");

tabButtons.forEach((button, index) => {
  button.addEventListener("click", () => {
    // Remover la clase 'active' de todos los botones y paneles
    tabButtons.forEach((btn) => btn.classList.remove("active"));
    tabPanes.forEach((pane) => pane.classList.remove("active"));

    // Agregar la clase 'active' al botón y panel correspondiente
    button.classList.add("active");
    tabPanes[index].classList.add("active");
  });
});
