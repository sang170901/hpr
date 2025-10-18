<?php
// Redirect từ /vnmt/materials.php về /materials.php
header('Location: /materials.php' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
exit;
?>