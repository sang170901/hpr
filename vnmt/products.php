<?php
// Redirect từ /vnmt/products.php về /products.php
header('Location: /products.php' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
exit;
?>