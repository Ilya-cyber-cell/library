<?php
require('./apiClass.php');
class BookClass extends APIClass
{
    public $bookId=NULL;
    public $Title=NULL;
    public $description=NULL;
    public $creatorLastName=NULL;
    public $creatorFirstName=NULL;
    public $creatorMidleName=NULL;
    public $creatorId=NULL;
    public $publisherId=NULL;  
    public $languageId=NULL;
    public $typeId=NULL;
    public $yearOfPublishing=NULL;
    public $identifier=NULL;
    public $genres=NULL;
    function loadFromBd($bookId) {
        try{
            $dbh = $this->dbh;
            $sth = $dbh->prepare("SELECT bookId, Title, description,  publisherId, languageId, identifier, typeId, yearOfPublishing,
                                    creators.LastName as LastName, creators.FirstName as FirstName , creators.midleName as midleName,books.creatorId as creatorId
                                    FROM books
                                    LEFT JOIN creators on creators.creatorId = books.creatorId
                                    WHERE bookId = :bookId");
            $sth->bindValue(':bookId', $bookId, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            
            $this->bookId=$bookId; 
            $this->Title=$result['Title']; 
            $this->description=$result['description']; 
            $this->creatorLastName=$result['LastName']; 
            $this->creatorFirstName=$result['FirstName']; 
            $this->creatorMidleName=$result['midleName']; 
            $this->creatorId=$result['creatorId']; 
            $this->publisherId=$result['publisherId']; 
            $this->languageId=$result['languageId']; 
            $this->typeId=$result['typeId'];
            $this->yearOfPublishing=$result['yearOfPublishing'];
            $this->identifier=$result['identifier']; 
            $sth = null;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        $this->genres = $this->getGenres();
    }
    function getGenres()
    {
        try{
            $dbh = $this->dbh;
            $bookId = $this->bookId;
            $stmt = $dbh->prepare("SELECT genresId FROM itemGenres 
                                    WHERE bookId = :bookId");
            $stmt->bindValue(':bookId', $bookId, PDO::PARAM_INT);
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $data[] = $row[0];
            }
            $stmt = null;
            return $data;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    function getAllDirectoryEntry($table)
    {
        try{
            $dbh = $this->dbh;
            $bookId = $this->bookId;
            $stmt = $dbh->prepare("SELECT id, name FROM ".$table." WHERE deleted = 0");
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $data[] =  array("id"=>$row[0],"label"=>$row[1]);
            }
            $stmt = null;
            return $data;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }    
    function getAvailability()
    {
        try{
            $dbh = $this->dbh;
            $bookId = $this->bookId;
                                    
            $stmt = $dbh->prepare("SELECT inventory.inventoryId  as inventoryId,state, expireDate,users.login as location,translation.ru as sateru
                        FROM  registery 
                        JOIN  inventory ON inventory.inventoryId = registery.inventoryId
                        JOIN  translation ON translation.en = registery.state
                        LEFT JOIN  users ON users.userId = registery.location
                        WHERE registeryid IN (SELECT max(registeryid) FROM registery 
                                    JOIN  inventory ON inventory.inventoryId = registery.inventoryId 
                                    WHERE bookId = :bookId
                                    GROUP BY inventory.inventoryId)");
            $stmt->bindValue(':bookId', $bookId, PDO::PARAM_INT);
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                $data[] = $row;
            }
            $stmt = null;
            return $data;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }  
    function save()
    { 
        if ($this->bookId == NULL){
            $query="INSERT INTO books(Title,description,creatorId,publisherId,languageId,identifier,typeId,yearOfPublishing,deleted)
                                    VALUES(:Title,:description,:creatorId,:publisherId,:languageId,:identifier,:typeId,:yearOfPublishing,0)";
        }else{
            $query="UPDATE books SET Title = :Title, description = :description,creatorId = :creatorId, publisherId = :publisherId,languageId = :languageId,
                                     identifier = :identifier, typeId = :typeId,yearOfPublishing=:yearOfPublishing
                    WHERE bookId = :bookId ";
        }
        try{
            $dbh = $this->dbh;
            $dbh->beginTransaction();
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':Title',     $this->Title,    PDO::PARAM_STR);
            $stmt->bindValue(':description',  $this->description, PDO::PARAM_STR);
            $stmt->bindValue(':creatorId', $this->creatorId,PDO::PARAM_INT);
            $stmt->bindValue(':publisherId', $this->publisherId,PDO::PARAM_INT);
            $stmt->bindValue(':languageId', $this->languageId,PDO::PARAM_INT);
            $stmt->bindValue(':typeId', $this->typeId,PDO::PARAM_INT);
            $stmt->bindValue(':yearOfPublishing', $this->yearOfPublishing,PDO::PARAM_INT);
            $stmt->bindValue(':identifier', $this->identifier,PDO::PARAM_STR);
            if ($this->bookId != NULL){
                $stmt->bindValue(':bookId',    $this->bookId,   PDO::PARAM_INT);
            }
            $stmt->execute();
            if ($this->bookId == NULL){
                $this->bookId =$dbh->lastInsertId();
            }
            $stmt = null;
            $stmt = $dbh->prepare('DELETE FROM itemGenres WHERE bookId = :bookId');
            $stmt->bindValue(':bookId',    $this->bookId,   PDO::PARAM_INT);
            $stmt->execute();
            $stmt = null;
            foreach ($this->genres as $genre) {
                $stmt = $dbh->prepare('INSERT INTO itemGenres(bookId,genresId)
                                                    VALUES(:bookId,:genresId)');
                $stmt->bindValue(':bookId',$this->bookId,   PDO::PARAM_INT);
                $stmt->bindValue(':genresId', $genre,   PDO::PARAM_INT);
                $stmt->execute();
                $stmt = null;
            }
            $dbh->commit();
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }    
    function getBook(){
        $Allgenres=$this->getAllDirectoryEntry('genres');
        $AllLanguage=$this->getAllDirectoryEntry('languages');
        $AllTypes=$this->getAllDirectoryEntry('types');
        $AllPublishers=$this->getAllDirectoryEntry('publishers');
        $AviableBooks=$this->getAvailability();
        return $this->toJson(0,array("book"=>$this,"Allgenres"=>$Allgenres,"AllLanguage"=>$AllLanguage,"AllTypes"=>$AllTypes,"AviableBooks"=>$AviableBooks,"AllPublishers"=>$AllPublishers));
    }
    function deleteBook($bookId)
    {
        $query="UPDATE books SET deleted = 1 WHERE bookId = :bookId ";
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':bookId', $bookId,   PDO::PARAM_INT);
            $stmt->execute();
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
    function reserveBook($bookId){
        $dbh = $this->dbh;
         try{
            $dbh->beginTransaction();    
            $query = "SELECT inventoryId  
                        FROM  registery 
                        WHERE state = \"available\" and 
                                        registeryid IN (SELECT max(registeryid) FROM registery 
                                        JOIN  inventory ON inventory.inventoryId = registery.inventoryId 
                                        WHERE bookId = :bookId
                                        GROUP BY inventory.inventoryId)
                        LIMIT 1";
            $sth = $dbh->prepare($query);
            $sth->bindValue(':bookId', $bookId, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            $inventoryId = $result['inventoryId']; 
            $sth=null;
            if ($inventoryId > 0){
                $date = new DateTime();
                $query = "INSERT INTO registery(date,expireDate,inventoryid,userId,location,state)
                        VALUES(:date,:expireDate,:inventoryid,:userId,:location,:state)";
                $stmt = $dbh->prepare($query);
                $stmt->bindValue(':date', $date->getTimestamp(),PDO::PARAM_INT);
                $stmt->bindValue(':expireDate', $date->getTimestamp() + (7*24*60*60),PDO::PARAM_INT);
                $stmt->bindValue(':inventoryid', $inventoryId,PDO::PARAM_INT);
                $stmt->bindValue(':userId',  $_SESSION['userId'],PDO::PARAM_INT);
                $stmt->bindValue(':location',$_SESSION['userId'],PDO::PARAM_INT);
                $stmt->bindValue(':state', "reserved",PDO::PARAM_STR);
                $stmt->execute();
                $stmt=null;
                $dbh->commit();
                return $this->toJson(0,"Зарезервирована книга ".strval($inventoryId));   
            }else{
                return $this->toJson(1,"Нет доступных экземпляров");
            }
            
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }    
}  
$book=new BookClass($dbh);    
switch ($method) {
case 'PUT':  
    $book->checkRights("editBook");
    $book->fromPostData();
    print($book->Save());    
    break;
  case 'GET':
    if (intval($request[0])>0){
        $book->loadFromBd(intval($request[0]),1);
    }
    print($book->getBook());
    break;
  case 'DELETE':
    $book->checkRights("deleteBook");
    print($book->deleteBook($request[0]));
    break;    
  case 'POST':
    if($request[1] == "reserv"){
        print($book->reserveBook($request[0]));
    }
    break;    
  default:
    var_dump($request);  
    break;
}

?>
