<?php
session_start();
session_unset();
session_destroy();
setcookie('remember_email', '', time() - 3600, '/', '', false, true);
header("location:../connexion/index.php");
exit();
?>
