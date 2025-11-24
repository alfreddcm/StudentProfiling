<?php
session_start();
// Destroy user session and redirect to login
session_unset();
session_destroy();
header('Location: ../../index.php');
exit();
?>
