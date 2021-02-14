<?php
$title="Книги ппользователя";
$query = "SELECT registery.inventoryId as  inventoryId, books.Title as Title, FROM_UNIXTIME(registery.expireDate,'%Y.%m.%d') as expireDate ,translation.ru as sateru
            FROM  registery 
            JOIN  inventory ON inventory.inventoryId = registery.inventoryId 
            JOIN  books ON books.bookId = inventory.bookID
            JOIN  translation ON translation.en = registery.state
            WHERE registeryid IN (SELECT max(registeryid) 
                                            FROM  registery 
                                            JOIN  inventory ON inventory.inventoryId = registery.inventoryId 
                                            WHERE location = :location
                                            GROUP BY inventory.inventoryId)
                AND (state = \"handedOut\" or state = \"reserved\" )";
$stmt = $dbh->prepare($query);        
$stmt->bindValue(':location', $request[1], PDO::PARAM_INT);
$stmt->execute();
$tableContent = $stmt->fetchAll();


?>
