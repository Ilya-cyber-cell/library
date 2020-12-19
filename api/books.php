<?php
require('./apiClass.php');

class BooksClass extends APIClass{
    function getBooks($creatorId){
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare("SELECT bookId, Title, description, creators.LastName as LastName, creators.FirstName as FirstName , creators.midleName as midleName
                                   FROM books
                                   LEFT JOIN creators on creators.creatorId = books.creatorId
                                   WHERE books.creatorId = :creatorId and deleted = 0");
            $stmt->bindValue(':creatorId', $creatorId, PDO::PARAM_INT);
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                $data[] = $row;
            }
            $stmt = null;
            return $this->toJson(0,$data);
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }

}

$books=new BooksClass($dbh);
switch ($method) {
  case 'GET':
    if ($request[0] >0){
        print($books->getBooks($request[0]));
    };
    break;
  default:
    var_dump($request);  
    break;
}


?>
