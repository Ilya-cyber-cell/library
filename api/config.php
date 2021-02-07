<?php
$dbUser='library';
$dbPass='BRcBAxYnDFbxXxFh';
$dbName='library3';
$dbHost='localhost';
$dayForReserv = 7;

try {
    $dbh = new PDO('mysql:host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "{\"error\":1,\"content\":\"" . $e->getMessage() . "\"}";
    die();
}

?>
