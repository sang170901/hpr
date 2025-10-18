<?php
// Redirect từ /vnmt/index.php về /index.php
header('Location: /' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
exit;
?>