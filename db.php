<?php
// Anzisha kipindi cha mtumiaji (Session) kwa ajili ya Login na Admin control
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'babati_maits_db';
$username = 'root';
$password = ''; // Weka password ya database yako kama ipo (kwa XAMPP ya kawaida huacha wazi)

try {
    // Kuunganisha na Database kupitia PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Kuweka hali ya makosa (Error Mode) kuwa Exception ili iwe rahisi kusoma tatizo likitokea
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Kuweka default fetch mode kuwa Associative Array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Kama kuna tatizo la muunganisho, simamisha mfumo na uonyeshe ujumbe
    die("<div style='background: #fee2e2; color: #991b1b; padding: 20px; font-family: sans-serif; border-radius: 8px; margin: 20px;'>
            <strong>Imeshindwa kuunganisha na Database!</strong><br>
            Tafadhali hakikisha umefungua XAMPP (Apache & MySQL) na ukatengeneza database inayoitwa <b>babati_maits_db</b>.<br><br>
            <small>Ujumbe wa kosa: " . $e->getMessage() . "</small>
         </div>");
}
?>