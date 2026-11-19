document.addEventListener("DOMContentLoaded", () => {
  const secciones = document.querySelectorAll("section");

  const mostrar = () => {
    secciones.forEach(sec => {
      const top = sec.getBoundingClientRect().top;
      if (top < window.innerHeight - 100) {
        sec.classList.add("visible");
      }
    });
  };

  window.addEventListener("scroll", mostrar);
  window.addEventListener("load", mostrar);
});
