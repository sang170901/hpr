<?php
// Redirect từ /vnmt/suppliers.php về /suppliers.php
header('Location: /suppliers.php' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
exit;
?>