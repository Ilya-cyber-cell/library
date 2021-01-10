<?php
require('./apiClass.php');
class creatorClass extends APIClass
{
    public    $creatorId=NULL;
    public    $LastName=NULL;
    public    $FirstName=NULL;
    public    $midleName=NULL;    
    function loadFromBd($creatorId) {
        try{
            $dbh = $this->dbh;
            $sth = $dbh->prepare("SELECT creatorId, LastName,FirstName,midleName FROM   creators  WHERE creatorId = :creatorId");
            $sth->bindValue(':creatorId', $creatorId, PDO::PARAM_INT);            
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            $this->creatorId=$result['creatorId']; 
            $this->LastName=$result['LastName']; 
            $this->FirstName=$result['FirstName']; 
            $this->midleName=$result['midleName'];             
            $sth = null;
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
    function save()
    {        
        if ($this->creatorId == NULL){
            $query="INSERT INTO creators (LastName,FirstName,midleName,deleted)
                                    VALUES(:LastName,:FirstName,:midleName,0)";
        }else{
            $query="UPDATE creators SET LastName = :LastName,FirstName = :FirstName,midleName = :midleName
                    WHERE creatorId = :creatorId ";
        }
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':LastName', $this->LastName,PDO::PARAM_STR);
            $stmt->bindValue(':FirstName', $this->FirstName,PDO::PARAM_STR);
            $stmt->bindValue(':midleName', $this->midleName,PDO::PARAM_STR);            
            if ($this->creatorId != NULL){
                $stmt->bindValue(':creatorId',    $this->creatorId,   PDO::PARAM_INT);
            }
            $stmt->execute();
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
    function getEntry(){
        return $this->toJson(0,array("directory"=>$this));
    }
    function deleteEntry($creatorId)
    {
        $query="UPDATE creators SET deleted = 1 WHERE creatorId = :creatorId ";
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':creatorId', $creatorId,   PDO::PARAM_INT);
            $stmt->execute();
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }

}   
$creator=new creatorClass($dbh);    
switch ($method) {
  case 'PUT':  
    $creator->checkRights("editCreator");
    $creator->fromPostData();
    print($creator->Save());    
    break;
  case 'GET':
    $creator->checkRights("editCreator");
    $creator->loadFromBd($request[0]);
    print($creator->getEntry());
    break;
  case 'DELETE':
    $creator->checkRights("editCreator");
    print($creator->deleteEntry($request[0]));
    break;    
  default:
    var_dump($request);  
    break;
}

?>
