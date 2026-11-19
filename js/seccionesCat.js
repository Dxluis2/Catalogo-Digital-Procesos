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

  const scrollBtn = document.querySelector('.btn-scroll-top');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      scrollBtn.style.display = 'flex';
    } else {
      scrollBtn.style.display = 'none';
    }
  });

/* Mostrar botón de scroll al hacer scroll */
window.onscroll = function() {
  const btn = document.querySelector('.btn-scroll-top');
  if (document.documentElement.scrollTop > 100) {
    btn.style.display = 'flex';
  } else {
    btn.style.display = 'none';
  }
};

