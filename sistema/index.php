<?php

require_once __DIR__ . '/includes/auth.php';

redirigir(autenticado() ? panel_de(rol_actual()) : 'login.php');
