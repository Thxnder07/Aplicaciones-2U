<?php
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base_url = $path . '/public/';
?>
<footer>
  <div class="container">
    <div class="footer-grid">
      <div>
        <h4>EventHub</h4>
        <p style="margin-top: 1rem; font-size: 0.9rem;">Plataforma líder en gestión de congresos y eventos académicos
          internacionales.</p>
      </div>
      <div>
        <h4>Enlaces</h4>
        <ul>
          <li><a href="index.php?view=eventos">Congresos</a></li>
          <li><a href="index.php?view=noticias">Noticias</a></li>
          <li><a href="index.php?view=contacto">Contacto</a></li>
        </ul>
      </div>
      <div>
        <h4>Legal</h4>
        <ul>
          <li><a href="#">Política de Privacidad</a></li>
          <li><a href="#">Términos y Condiciones</a></li>
        </ul>
      </div>
    </div>
    <div class="copyright">
      <p>© 2025 EventHub. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>

<script src="<?php echo $base_url; ?>js/main.js"></script>
</body>

</html>