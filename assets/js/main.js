/**
 * Scripts principales del sistema
 * Sistema RIAAC
 */

$(document).ready(function () {
  // Inicializar DataTables con configuración en español
  if ($.fn.DataTable) {
    $(".datatable").DataTable({
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
      },
      responsive: true,
      pageLength: 10,
      order: [[0, "desc"]],
    });
  }

  // Confirmación de eliminación
  $(".btn-delete").on("click", function (e) {
    e.preventDefault();
    const url = $(this).attr("href");
    const nombre = $(this).data("nombre") || "este registro";

    Swal.fire({
      title: "¿Estás seguro?",
      text: `¿Deseas eliminar ${nombre}?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = url;
      }
    });
  });

  // Validación de formularios
  $("form").on("submit", function (e) {
    const form = this;
    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
    }
    $(form).addClass("was-validated");
  });

  // Auto-cerrar alertas después de 5 segundos
  $(".alert").not(".alert-permanent").delay(5000).fadeOut("slow");
});

/**
 * Funciones globales
 */

// Mostrar mensaje de éxito
function showSuccess(mensaje) {
  Swal.fire({
    icon: "success",
    title: "¡Éxito!",
    text: mensaje,
    timer: 3000,
    showConfirmButton: false,
  });
}

// Mostrar mensaje de error
function showError(mensaje) {
  Swal.fire({
    icon: "error",
    title: "Error",
    text: mensaje,
  });
}

// Formatear moneda
function formatearMoneda(numero) {
  return (
    "S/ " +
    parseFloat(numero)
      .toFixed(2)
      .replace(/\d(?=(\d{3})+\.)/g, "$&,")
  );
}

// Formatear fecha DD/MM/YYYY
function formatearFecha(fecha) {
  if (!fecha) return "-";
  const partes = fecha.split("-");
  return `${partes[2]}/${partes[1]}/${partes[0]}`;
}
