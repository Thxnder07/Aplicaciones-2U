<?php 
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $base_url = $path . '/public/';
?>
     <footer>
    <div class="container">
      <p>© 2025 Portal de Congresos y Seminarios | Todos los derechos reservados</p>
      <div class="redes">
        <a href="#"><img src="public/iconos/facebook.svg" alt="Facebook"></a>
        <a href="#"><img src="public/iconos/twitter.svg" alt="Twitter"></a>
        <a href="#"><img src="public/iconos/linkedin.svg" alt="LinkedIn"></a>
      </div>
    </div>
  </footer>

    <script src="<?php echo $base_url; ?>js/main.js"></script>
</body>
</html>