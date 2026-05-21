<?php
session_start();
session_unset();
session_destroy();

// Tendang balik ke halaman login utama
header("Location: login.php");
exit();
?>