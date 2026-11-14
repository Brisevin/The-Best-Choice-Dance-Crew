<?php
// Anzisha kipindi cha mtumiaji
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Futa data zote za session
$_SESSION = array();

// Vunja au haribu session yote
session_destroy();

// Mwelekeze mtumiaji kwenye ukurasa wa Login
header("Location: auth.php");
exit;
?>