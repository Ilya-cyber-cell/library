<?php

$begin = $request[1];
$end = $request[2];

$title="Мои книги";
$query = "SELECT registery.inventoryId as  inventoryId, books.Title as Title, FROM_UNIXTIME(registery.date,'%Y.%m.%d') as date ,translation.ru as sateru,
            CONCAT(users.lastName,' ' ,users.firstName, ' ',users.midleName) as fio ,
            CONCAT(localtion.lastName,' ' ,localtion.firstName, ' ',localtion.midleName) as localtion_fio 
            FROM  registery 
            JOIN  inventory ON inventory.inventoryId = registery.inventoryId 
            JOIN  books ON books.bookId = inventory.bookID
            JOIN  users ON users.userId = registery.userid
            LEFT JOIN  users as localtion ON users.userId = registery.location
            JOIN  translation ON translation.en = registery.state
            WHERE registery.date > :begin and registery.date < :end
            ORDER BY registery.date";
$stmt = $dbh->prepare($query);        
$stmt->bindValue(':begin', $begin, PDO::PARAM_INT);
$stmt->bindValue(':end', $end, PDO::PARAM_INT);

$stmt->execute();
$tableContent = $stmt->fetchAll();

?>
