<?php
require('./apiClass.php');
class IventoryClass extends APIClass
{
    public $bookId=NULL;
    public $act=NULL;
    function addCopy(){
        $bookID = $this->bookId;
        $query = "INSERT INTO inventory(bookID,deleted)
                        VALUES(:bookID,0)";
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':bookID', $bookID,PDO::PARAM_INT);
            $stmt->execute();
            $inventoryId = $dbh->lastInsertId();
            $stmt=null;
            $this->changeStatus($inventoryId,"available");
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
    function deleteCopy($inventoryId){
        $query = "UPDATE inventoryId SET deleted = 1 WHERE inventoryId = :inventoryId";
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':inventoryId', $inventoryId,PDO::PARAM_INT);
            $stmt->execute();
            $stmt=null;
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }    
    function changeStatus($inventoryid,$state,$expireDate=null,$location=null){
        $date = new DateTime();
        if (!isset($_SESSION['userId'])){
            print ($this->toJson(1,"Вы не авторизованы"));
            die(0);
        }
        $query = "INSERT INTO registery(date,expireDate,inventoryid,userId,location,act,state)
                        VALUES(:date,:expireDate,:inventoryid,:userId,:location,:act,:state)";
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':date', $date->getTimestamp(),PDO::PARAM_INT);
            $stmt->bindValue(':expireDate', $expireDate,PDO::PARAM_INT);
            $stmt->bindValue(':inventoryid', $inventoryid,PDO::PARAM_INT);
            $stmt->bindValue(':userId', $_SESSION['userId'],PDO::PARAM_INT);
            $stmt->bindValue(':location',$location,PDO::PARAM_INT);
            $stmt->bindValue(':act', $this->act,PDO::PARAM_STR);
            $stmt->bindValue(':state', $state,PDO::PARAM_STR);
            $stmt->execute();
            $stmt=null;
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
}  
$inventory=new IventoryClass($dbh);  
switch ($method) {
  case 'POST':
    $inventory->checkRights("editBook");
    if ($request[1] == 'In'){
        print($inventory->changeStatus($request[0],"available"));
    }else{
        $json = json_decode($_POST['json']);
        print($inventory->changeStatus($request[0],"handedOut",$json->selectedDate,$json->selectedUser));
    }
    break;  
  case 'PUT':  
    $inventory->checkRights("editBook");
    $inventory->fromPostData();
    print($inventory->addCopy());    
    break;
  case 'DELETE':
    $inventory->checkRights("deleteBook");
    print($inventory->deleteCopy($request[0]));
    break;     
  default:
    var_dump($request);  
    break;
}

?>
