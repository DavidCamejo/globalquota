<?php
style('globalquota', 'admin');

script('globalquota', 'admin');

$limitGB = \OC::$server->getConfig()->getAppValue('globalquota', 'limit_gb', 500);

?>

<div class="section">
  <h2>Global Quota</h2>
  <form id="globalquota-form" method="post">
    <?php p(\OC::$server->getCsrfTokenManager()->getHiddenTokenHtml()); ?>
    <label for="limit_gb">Límite global de almacenamiento (GB):</label>
    <input type="number" id="limit_gb" name="limit_gb" value="<?php p($limitGB); ?>" min="1" step="1">
    <input type="submit" value="Guardar" class="button">
  </form>
</div>
