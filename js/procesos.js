// Obtener parámetros de la URL
const urlParams = new URLSearchParams(window.location.search);
const id = urlParams.get('id');
const categoriaParam = urlParams.get('categoria');

const form = document.getElementById('formProceso');
const tituloFormulario = document.getElementById('tituloFormulario');
const inputId = document.getElementById('id');

if (id && categoriaParam) {
  // Edición
  tituloFormulario.textContent = "Editar Proceso";
  form.action = "php/editar_proceso.php";
  
  // Cargar datos del proceso
  fetch(`php/obtener_proceso.php?id=${id}&categoria=${categoriaParam}`)
    .then(res => res.json())
    .then(data => {
      if (data) {
        inputId.value = data.id;
        document.getElementById('titulo').value = data.titulo;
        document.getElementById('descripcion').value = data.descripcion;
        document.getElementById('categoria').value = data.categoria || categoriaParam;
        // No cargamos imágenes por seguridad (file input)
      } else {
        alert("Proceso no encontrado.");
        window.location.href = "procesos.html";
      }
    });
} else {
  form.action = "php/guardar_proceso.php";
}
