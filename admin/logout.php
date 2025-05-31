<?php
session_start();
session_destroy();

// Cegah cache halaman logout juga
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

header("Location: http://localhost/bbksda/admin/login.php");
exit();
