<?php
session_start();
$_SESSION['test'] = 'ok';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
?>