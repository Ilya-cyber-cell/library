<?php
$dbUser='library';
$dbPass='BRcBAxYnDFbxXxFh';
$dbName='library';
$dbHost='localhost';
$dayForReserv = 7;

try {
    $dbh = new PDO('mysql:host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

?>
