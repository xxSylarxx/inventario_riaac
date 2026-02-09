/**
 * JS para módulo de hosting
 * Cálculo automático de fecha de vencimiento
 */

$(document).ready(function () {
  // Calcular fecha de vencimiento (1 año desde fecha inicio)
  $("#fecha_inicio").on("change", function () {
    const fechaInicio = new Date($(this).val());
    if (fechaInicio) {
      // Añadir 1 año
      fechaInicio.setFullYear(fechaInicio.getFullYear() + 1);
      const fechaVenc = fechaInicio.toISOString().split("T")[0];
      $("#fecha_vencimiento").val(fechaVenc);
    }
  });
});
