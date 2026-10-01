(function () {
  var contenedor = document.querySelector('[data-avisos]');

  var iniciarToast = function (toast) {
    var instancia = bootstrap.Toast.getOrCreateInstance(toast);
    var tiempo = toast.querySelector('.vs-toast-tiempo');

    if (tiempo) {
      tiempo.style.setProperty('--vs-duracion', (parseInt(toast.dataset.bsDelay || '5000', 10)) + 'ms');
      // Bootstrap reinicia la cuenta completa al sacar el mouse: la barra tambien.
      toast.addEventListener('mouseleave', function () {
        tiempo.style.animation = 'none';
        void tiempo.offsetWidth;
        tiempo.style.animation = '';
      });
    }

    toast.addEventListener('hidden.bs.toast', function () { toast.remove(); });
    instancia.show();
  };

  var aviso = function (tipo, titulo, mensaje) {
    if (!contenedor) {
      contenedor = document.createElement('div');
      contenedor.className = 'atrax-toasts';
      contenedor.setAttribute('aria-live', 'polite');
      document.body.appendChild(contenedor);
    }

    var toast = document.createElement('div');
    toast.className = 'toast vs-toast ' + tipo;
    toast.setAttribute('role', tipo === 'error' ? 'alert' : 'status');
    toast.setAttribute('aria-atomic', 'true');
    if (tipo === 'error') {
      toast.dataset.bsDelay = '8000';
    }

    var icono = document.createElement('span');
    icono.className = 'vs-toast-icono';
    icono.setAttribute('aria-hidden', 'true');
    icono.textContent = tipo === 'error' ? '!' : '✓';

    var cuerpo = document.createElement('div');
    cuerpo.className = 'vs-toast-cuerpo';
    var tituloEl = document.createElement('p');
    tituloEl.className = 'vs-toast-titulo';
    tituloEl.textContent = titulo;
    var texto = document.createElement('p');
    texto.className = 'mb-0';
    texto.textContent = mensaje;
    cuerpo.append(tituloEl, texto);

    var cerrar = document.createElement('button');
    cerrar.type = 'button';
    cerrar.className = 'btn-close';
    cerrar.setAttribute('data-bs-dismiss', 'toast');
    cerrar.setAttribute('aria-label', 'Cerrar aviso');

    var tiempo = document.createElement('span');
    tiempo.className = 'vs-toast-tiempo';
    tiempo.setAttribute('aria-hidden', 'true');

    toast.append(icono, cuerpo, cerrar, tiempo);
    contenedor.appendChild(toast);
    iniciarToast(toast);
  };

  document.querySelectorAll('[data-avisos] .toast').forEach(iniciarToast);

  // Ventana de confirmacion generica: data-confirmar en formularios y enlaces.

  var modalEl = document.getElementById('modalConfirmar');
  var modal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;
  var alAceptar = null;

  var completar = function (texto, formulario) {
    if (!formulario) return texto;
    return texto.replace(/\{(\w+)\}/g, function (todo, nombre) {
      var campo = formulario.elements[nombre];
      if (!campo) return todo;
      return campo.tagName === 'SELECT' && campo.selectedOptions.length
        ? campo.selectedOptions[0].textContent.trim()
        : campo.value;
    });
  };

  var confirmar = function (origen, accion) {
    var formulario = origen.tagName === 'FORM' ? origen : null;
    var texto = completar(origen.dataset.confirmar || '', formulario);

    if (!modal) {
      if (window.confirm(texto)) accion();
      return;
    }

    var peligro = origen.dataset.confirmarTipo === 'peligro';
    var boton = modalEl.querySelector('[data-modal-aceptar]');
    var icono = modalEl.querySelector('[data-modal-icono]');

    modalEl.querySelector('[data-modal-titulo]').textContent = completar(origen.dataset.confirmarTitulo || 'Confirmar', formulario);
    modalEl.querySelector('[data-modal-texto]').textContent = texto;
    boton.textContent = origen.dataset.confirmarBoton || 'Confirmar';
    boton.classList.toggle('btn-danger', peligro);
    boton.classList.toggle('btn-primary', !peligro);
    icono.classList.toggle('peligro', peligro);
    icono.textContent = peligro ? '!' : '?';
    modalEl.dataset.peligro = peligro ? '1' : '';

    alAceptar = accion;
    modal.show();
  };

  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var foco = modalEl.dataset.peligro
        ? modalEl.querySelector('[data-bs-dismiss]')
        : modalEl.querySelector('[data-modal-aceptar]');
      foco.focus();
    });

    modalEl.querySelector('[data-modal-aceptar]').addEventListener('click', function () {
      var accion = alAceptar;
      alAceptar = null;
      modal.hide();
      if (accion) accion();
    });
  }

  var enviarConfirmado = function (formulario, boton) {
    formulario.dataset.confirmado = '1';
    if (formulario.requestSubmit) {
      formulario.requestSubmit(boton || undefined);
    } else {
      formulario.submit();
    }
  };

  document.addEventListener('submit', function (evento) {
    var formulario = evento.target;

    if (evento.defaultPrevented || !formulario.matches('form[data-confirmar]')) return;

    if (formulario.dataset.confirmado === '1') {
      delete formulario.dataset.confirmado;
      return;
    }

    var condicion = formulario.dataset.confirmarCuando;
    if (condicion) {
      var partes = condicion.split(':');
      var campo = formulario.elements[partes[0]];
      if (!campo || campo.value !== partes[1]) return;
    }

    evento.preventDefault();
    var boton = evento.submitter;
    confirmar(formulario, function () { enviarConfirmado(formulario, boton); });
  });

  document.addEventListener('click', function (evento) {
    var enlace = evento.target.closest('a[data-confirmar]');

    if (!enlace || evento.defaultPrevented || evento.button !== 0
        || evento.metaKey || evento.ctrlKey || evento.shiftKey || evento.altKey) return;

    evento.preventDefault();
    confirmar(enlace, function () {
      document.dispatchEvent(new CustomEvent('vs:navegando'));
      window.location.href = enlace.href;
    });
  });

  // Resumen del pedido antes de confirmarlo (cliente/pedido.php).

  var formPedido = document.querySelector('form[data-resumen-pedido]');
  var modalPedidoEl = document.getElementById('modalPedido');

  if (formPedido && modalPedidoEl) {
    var modalPedido = bootstrap.Modal.getOrCreateInstance(modalPedidoEl);

    var moneda = function (valor) {
      return '$ ' + String(Math.round(valor)).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    var fecha = function (valor) {
      var partes = (valor || '').split('-');
      return partes.length === 3 ? partes[2] + '/' + partes[1] + '/' + partes[0] : valor;
    };

    formPedido.addEventListener('submit', function (evento) {
      if (evento.defaultPrevented) return;

      if (formPedido.dataset.confirmado === '1') {
        delete formPedido.dataset.confirmado;
        return;
      }

      evento.preventDefault();

      var plan = document.getElementById('plan_id').selectedOptions[0];
      var esperado = parseInt(plan.dataset.viandas || '0', 10);
      var lista = modalPedidoEl.querySelector('[data-resumen="lineas"]');
      var total = 0;
      var viandas = 0;

      lista.textContent = '';

      formPedido.querySelectorAll('input[data-vianda]').forEach(function (campo) {
        var cantidad = parseInt(campo.value || '0', 10) || 0;
        if (campo.disabled || cantidad <= 0) return;

        var subtotal = cantidad * parseFloat(campo.dataset.precio || '0');
        var item = document.createElement('li');
        var nombre = document.createElement('span');
        var importe = document.createElement('span');

        nombre.textContent = cantidad + ' × ' + campo.dataset.nombre;
        importe.textContent = moneda(subtotal);
        importe.className = 'fw-semibold';
        item.append(nombre, importe);
        lista.appendChild(item);

        total += subtotal;
        viandas += cantidad;
      });

      if (viandas === 0) {
        aviso('error', 'Falta elegir los platos', 'Elegi al menos un plato para armar tu pedido.');
        return;
      }

      if (viandas !== esperado) {
        aviso('error', 'Revisa las cantidades',
          'El plan ' + plan.dataset.nombre + ' lleva exactamente ' + esperado + ' viandas y elegiste ' + viandas + '.');
        return;
      }

      modalPedidoEl.querySelector('[data-resumen="plan"]').textContent = plan.dataset.nombre + ' · ' + esperado + ' viandas';
      modalPedidoEl.querySelector('[data-resumen="fecha"]').textContent = fecha(document.getElementById('fecha_entrega').value);
      modalPedidoEl.querySelector('[data-resumen="total"]').textContent = moneda(total);

      if (contenedor) {
        contenedor.querySelectorAll('.vs-toast.error').forEach(function (toast) {
          bootstrap.Toast.getOrCreateInstance(toast).hide();
        });
      }

      modalPedido.show();
    });

    modalPedidoEl.querySelector('[data-pedido-confirmar]').addEventListener('click', function () {
      modalPedido.hide();
      enviarConfirmado(formPedido);
    });
  }

  window.ViandaSegura = window.ViandaSegura || {};
  window.ViandaSegura.aviso = aviso;
})();
