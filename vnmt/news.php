<?php
// Redirect từ /vnmt/news.php về /news.php
header('Location: /vnmt/news-modern.php' . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']));
exit;
?>