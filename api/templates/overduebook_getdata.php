<?php
$title="Мои книги";
$query = "SELECT registery.inventoryId as  inventoryId, books.Title as Title, FROM_UNIXTIME(registery.expireDate,'%Y.%m.%d') as expireDate ,translation.ru as sateru,
            CONCAT(users.lastName,' ' ,users.firstName, ' ',users.midleName) as fio
            FROM  registery 
            JOIN  inventory ON inventory.inventoryId = registery.inventoryId 
            JOIN  books ON books.bookId = inventory.bookID
            JOIN  users ON users.userId = registery.location
            JOIN  translation ON translation.en = registery.state
            WHERE registeryid IN (SELECT max(registeryid) 
                                            FROM  registery 
                                            GROUP BY registery.inventoryId)
                AND (state = \"handedOut\" or state = \"reserved\" ) AND expireDate > CURRENT_TIMESTAMP";
$stmt = $dbh->prepare($query);        
$stmt->bindValue(':location', $_SESSION['userId'], PDO::PARAM_INT);
$stmt->execute();
$tableContent = $stmt->fetchAll();


?>
