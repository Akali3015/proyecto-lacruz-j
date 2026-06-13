<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/popper.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/bootstrap.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/jquery-3.7.1.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/jquery.mask.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/jquery.dataTables.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/select2.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/sweetalert2.all.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/chart.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/notifier.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/datepicker-full.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/datepicker.min.es.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/library/socket.io.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/node_modules/leaflet/dist/leaflet.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/driver.min.js"></script><!--Driverjs -->
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php
$directorioJs = '/proyecto-lacruz-j/src/assets/js/modulos/';
if ($_SESSION['vistaActual'] == 'login') {
  $archivoModulo = $directorioJs . 'usuarios.js';
} else {
  $archivoModulo = $directorioJs . $_SESSION['vistaActual'] . '.js';
}
?>
<script type="module" nonce="<?php echo $_SESSION['nonce']; ?>" src="<?php echo $archivoModulo ?>"></script>
</body>

</html>

<?php
ob_end_flush();
?>