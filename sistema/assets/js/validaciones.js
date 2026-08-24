(function () {
  document.querySelectorAll('form[data-validar]').forEach(function (formulario) {
    formulario.addEventListener('submit', function (evento) {
      if (!formulario.checkValidity()) {
        evento.preventDefault();
        evento.stopPropagation();
      }
      formulario.classList.add('was-validated');
    });
  });

  document.querySelectorAll('[data-ver-clave]').forEach(function (boton) {
    boton.addEventListener('click', function () {
      var campo = document.getElementById(boton.dataset.verClave);
      if (!campo) return;
      var visible = campo.type === 'text';
      campo.type = visible ? 'password' : 'text';
      boton.textContent = visible ? 'ver' : 'ocultar';
    });
  });

  var contador = document.querySelector('[data-total-viandas]');
  if (contador) {
    var campos = document.querySelectorAll('input[data-vianda]');
    var objetivo = document.getElementById('plan_id');

    var actualizar = function () {
      var total = 0;
      campos.forEach(function (campo) {
        total += parseInt(campo.value || '0', 10) || 0;
      });
      var esperado = objetivo && objetivo.selectedOptions.length
        ? parseInt(objetivo.selectedOptions[0].dataset.viandas || '0', 10)
        : 0;
      contador.textContent = total + ' / ' + esperado;
      contador.classList.toggle('text-danger', total !== esperado);
      contador.classList.toggle('text-success', total === esperado && total > 0);
    };

    campos.forEach(function (campo) { campo.addEventListener('input', actualizar); });
    if (objetivo) objetivo.addEventListener('change', actualizar);
    actualizar();
  }
})();
