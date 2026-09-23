<?php
session_start();
$_SESSION = []; 
session_destroy();

header("Location: ../View/v_login.php"); 
exit;