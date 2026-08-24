<?php if (autenticado()): ?>
      </main>
    </div>
  </div>
</div>
<footer class="atrax-pie">
  <p class="mb-0"><?= APP_NOMBRE ?> &copy; 2026 — Produccion y distribucion de viandas, Montevideo.</p>
</footer>
<?php else: ?>
</main>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/validaciones.js') ?>"></script>
</body>
</html>
