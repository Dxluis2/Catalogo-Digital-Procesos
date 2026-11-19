let procesos = [];
let procesosOriginales = [];
let indiceActual = 0;

async function cargarProcesos(categoria) {
  try {
    const respuesta = await fetch(`php/procesos.php?equipo=${categoria}`);
    procesosOriginales = await respuesta.json();
    // Agregar categoría al objeto para facilitar edición y eliminación
    procesosOriginales = procesosOriginales.map(p => ({ ...p, categoria }));
    procesos = procesosOriginales.slice(); // copia para filtros
    indiceActual = 0;

    if (procesos.length > 0) {
      mostrarProceso(indiceActual);
      document.getElementById("btnSiguiente").disabled = procesos.length === 1;
    } else {
      document.getElementById("tituloProceso").textContent = "No hay procesos para esta categoría.";
      document.getElementById("mediaProceso").innerHTML = "";
      document.getElementById("descripcionProceso").textContent = "";
      document.getElementById("btnAtras").disabled = true;
      document.getElementById("btnSiguiente").disabled = true;
      ocultarBotonesEditarEliminar();
    }
  } catch (error) {
    console.error("Error al cargar procesos:", error);
    document.getElementById("tituloProceso").textContent = "Error al cargar procesos.";
    ocultarBotonesEditarEliminar();
  }
}

function mostrarProceso(indice) {
  const titulo = document.getElementById("tituloProceso");
  const descripcion = document.getElementById("descripcionProceso");
  const mediaDiv = document.getElementById("mediaProceso");
  const btnAtras = document.getElementById("btnAtras");
  const btnSiguiente = document.getElementById("btnSiguiente");
  const btnEditar = document.getElementById("btnEditar");
  const btnEliminar = document.getElementById("btnEliminar");

  if (!procesos[indice]) {
    ocultarBotonesEditarEliminar();
    return;
  }

  const proceso = procesos[indice];

  titulo.textContent = proceso.titulo;
  descripcion.textContent = proceso.descripcion;
  mediaDiv.innerHTML = "";

if (proceso.media_tipo === "imagen" && proceso.media_url) {
  const urls = proceso.media_url.split(",").map(u => u.trim()).filter(u => u.length > 0);
  urls.forEach(url => {
    let imgUrl = url;
    // Si la URL no empieza con http o /media/, añade el prefijo "media/"
    if (!url.startsWith("http") && !url.startsWith("/media/") && !url.startsWith("media/")) {
      imgUrl = "media/" + url;
    }
    console.log("Cargando imagen:", imgUrl); // <-- LOG para debug

    const img = document.createElement("img");
    img.src = imgUrl;
    img.alt = proceso.titulo;
    img.style.maxWidth = "300px";
    img.style.marginRight = "10px";
    img.style.marginBottom = "10px";
    img.classList.add("img-thumbnail");
    mediaDiv.appendChild(img);
  });
} else {
  mediaDiv.textContent = "No hay imágenes para este proceso.";
}

  btnAtras.disabled = indice === 0;
  btnSiguiente.disabled = indice === procesos.length - 1;

  btnEditar.classList.remove("d-none");
  btnEliminar.classList.remove("d-none");
}

function ocultarBotonesEditarEliminar() {
  document.getElementById("btnEditar").classList.add("d-none");
  document.getElementById("btnEliminar").classList.add("d-none");
}

document.getElementById("btnAtras").addEventListener("click", () => {
  if (indiceActual > 0) {
    indiceActual--;
    mostrarProceso(indiceActual);
  }
});

document.getElementById("btnSiguiente").addEventListener("click", () => {
  if (indiceActual < procesos.length - 1) {
    indiceActual++;
    mostrarProceso(indiceActual);
  }
});

document.getElementById("categoria").addEventListener("change", (e) => {
  const categoria = e.target.value;
  if (categoria) {
    document.getElementById("busqueda").value = "";
    cargarProcesos(categoria);
  } else {
    document.getElementById("tituloProceso").textContent = "Selecciona una categoría";
    document.getElementById("mediaProceso").innerHTML = "";
    document.getElementById("descripcionProceso").textContent = "";
    document.getElementById("btnAtras").disabled = true;
    document.getElementById("btnSiguiente").disabled = true;
    ocultarBotonesEditarEliminar();
  }
});

document.getElementById("busqueda").addEventListener("input", (e) => {
  const texto = e.target.value.toLowerCase();

  procesos = procesosOriginales.filter(p => p.titulo.toLowerCase().includes(texto));
  indiceActual = 0;

  if (procesos.length > 0) {
    mostrarProceso(indiceActual);
    document.getElementById("btnSiguiente").disabled = procesos.length === 1;
  } else {
    document.getElementById("tituloProceso").textContent = "No se encontraron procesos que coincidan con la búsqueda.";
    document.getElementById("mediaProceso").innerHTML = "";
    document.getElementById("descripcionProceso").textContent = "";
    document.getElementById("btnAtras").disabled = true;
    document.getElementById("btnSiguiente").disabled = true;
    ocultarBotonesEditarEliminar();
  }
});

document.getElementById("btnEditar").addEventListener("click", () => {
  const proceso = procesos[indiceActual];
  if (proceso) {
    window.location.href = `formulario.html?id=${proceso.id}&categoria=${encodeURIComponent(proceso.categoria)}`;
  }
});

document.getElementById("btnEliminar").addEventListener("click", async () => {
  const proceso = procesos[indiceActual];
  if (!proceso) return;

  if (confirm(`¿Seguro que quieres eliminar el proceso "${proceso.titulo}"?`)) {
    try {
      const res = await fetch("php/eliminar_proceso.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id=${proceso.id}&categoria=${encodeURIComponent(proceso.categoria)}`
      });
      const data = await res.text();
      alert(data);
      cargarProcesos(proceso.categoria);
    } catch (error) {
      alert("Error al eliminar el proceso.");
      console.error(error);
    }
  }
});
