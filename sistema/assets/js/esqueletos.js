(function () {
  // Al navegar, el contenido se reemplaza por un esqueleto (placeholders de
  // Bootstrap) hasta que llega la pagina nueva, en vez de quedar congelado.

  var principal = document.querySelector('.atrax-main');
  var demora = 100;
  var temporizador = null;
  var botonOriginal = null;
  var menuOriginal = null;

  var p = function (clases) {
    return '<span class="placeholder ' + clases + '"></span>';
  };

  var repetir = function (veces, html) {
    return new Array(veces + 1).join(html);
  };

  var encabezado = function () {
    return '<div class="encabezado-pagina"><div class="w-100">'
      + p('col-6 col-md-3 vs-titulo') + p('col-10 col-md-5 vs-linea')
      + '</div></div>';
  };

  var tabla = function (filas) {
    return '<section class="panel"><header>' + p('col-4 col-md-2 vs-linea') + '</header>'
      + repetir(filas, '<div class="vs-fila">'
        + p('col-1 vs-linea') + p('col-3 vs-linea') + p('col-2 vs-linea d-none d-md-block')
        + p('col-2 vs-linea') + p('col-2 vs-pildora') + '</div>')
      + '</section>';
  };

  var tarjetas = function (cantidad, clases) {
    return '<div class="row g-3 mb-4">'
      + repetir(cantidad, '<div class="' + clases + '"><div class="tarjeta">'
        + p('col-6 vs-chico') + p('col-4 vs-numero') + p('col-9 vs-chico') + '</div></div>')
      + '</div>';
  };

  var formulario = function () {
    return '<div class="row g-3"><div class="col-lg-8">' + tabla(4) + '</div>'
      + '<div class="col-lg-4"><section class="panel">'
      + repetir(3, p('col-5 vs-chico') + p('col-12 vs-campo'))
      + p('col-12 vs-boton') + '</section></div></div>';
  };

  var variantes = {
    panel: function () { return encabezado() + tarjetas(4, 'col-6 col-xl-3') + tabla(5); },
    tarjetas: function () { return encabezado() + tarjetas(6, 'col-md-6 col-xl-4'); },
    formulario: function () { return encabezado() + formulario(); },
    tabla: function () { return encabezado() + tabla(7); }
  };

  var varianteDe = function (ruta) {
    if (/\/(admin|operador)\/(index\.php)?$/.test(ruta)) return 'panel';
    if (/\/cliente\/(index\.php)?$/.test(ruta)) return 'tarjetas';
    if (/\/(cliente\/pedido|operador\/produccion)\.php$/.test(ruta)) return 'formulario';
    return 'tabla';
  };

  var mostrar = function (ruta) {
    clearTimeout(temporizador);
    temporizador = setTimeout(function () {
      document.body.classList.add('vs-navegando');
      if (!principal) return;

      var esqueleto = document.createElement('div');
      esqueleto.className = 'vs-esqueleto placeholder-glow';
      esqueleto.setAttribute('aria-hidden', 'true');
      esqueleto.innerHTML = variantes[varianteDe(ruta)]();

      principal.querySelectorAll('.vs-esqueleto').forEach(function (viejo) { viejo.remove(); });
      principal.appendChild(esqueleto);
      principal.classList.add('cargando');
      principal.setAttribute('aria-busy', 'true');
    }, demora);
  };

  var ocultar = function () {
    clearTimeout(temporizador);
    document.body.classList.remove('vs-navegando');

    if (principal) {
      principal.classList.remove('cargando');
      principal.removeAttribute('aria-busy');
      principal.querySelectorAll('.vs-esqueleto').forEach(function (viejo) { viejo.remove(); });
    }

    if (botonOriginal) {
      botonOriginal.boton.innerHTML = botonOriginal.html;
      botonOriginal.boton.disabled = false;
      botonOriginal = null;
    }

    if (menuOriginal) {
      menuOriginal.nuevo.classList.remove('active');
      menuOriginal.previos.forEach(function (previo) { previo.classList.add('active'); });
      menuOriginal = null;
    }
  };

  var marcarActivo = function (enlace) {
    var menu = enlace.closest('.atrax-sidebar, .atrax-pills');
    if (!menu || enlace.classList.contains('active')) return;

    var previos = Array.prototype.slice.call(menu.querySelectorAll('.active'));
    previos.forEach(function (activo) { activo.classList.remove('active'); });
    enlace.classList.add('active');
    menuOriginal = { nuevo: enlace, previos: previos };
  };

  document.addEventListener('click', function (evento) {
    var enlace = evento.target.closest('a[href]');

    if (!enlace || evento.defaultPrevented || evento.button !== 0
        || evento.metaKey || evento.ctrlKey || evento.shiftKey || evento.altKey) return;

    if (enlace.target && enlace.target !== '_self') return;
    if (enlace.hasAttribute('download') || enlace.hasAttribute('data-bs-toggle') || enlace.hasAttribute('data-sin-esqueleto')) return;

    var destino = new URL(enlace.href, window.location.href);
    if (destino.origin !== window.location.origin) return;
    if (destino.pathname === window.location.pathname && destino.search === window.location.search && destino.hash) return;

    marcarActivo(enlace);
    mostrar(destino.pathname);
  });

  document.addEventListener('submit', function (evento) {
    if (evento.defaultPrevented) return;

    var formulario = evento.target;
    var boton = evento.submitter || formulario.querySelector('[type="submit"]');

    if (boton && !boton.disabled) {
      botonOriginal = { boton: boton, html: boton.innerHTML };
      boton.style.minWidth = boton.offsetWidth + 'px';
      boton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>'
        + '<span role="status">Procesando...</span>';
      // Se deshabilita despues de que el navegador toma los datos del envio.
      setTimeout(function () { boton.disabled = true; }, 0);
    }

    mostrar(window.location.pathname);
  });

  document.addEventListener('vs:navegando', function () {
    mostrar(window.location.pathname);
  });

  // Al volver con el boton "atras" el navegador puede restaurar la pagina tal cual quedo.
  window.addEventListener('pageshow', function (evento) {
    if (evento.persisted) ocultar();
  });
})();
