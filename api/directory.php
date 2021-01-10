<?php
require('./apiClass.php');
class directoryEntry extends directoryEditor
{
    public    $id=NULL;
    public    $name=NULL;
    function loadFromBd($id,$tableName) {
        try{
            $dbh = $this->dbh;
            $sth = $dbh->prepare("SELECT id, name FROM   $tableName  WHERE id = :id");
            $sth->bindValue(':id', $id, PDO::PARAM_INT);            
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            $this->id=$result['id']; 
            $this->name=$result['name']; 
            $sth = null;
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
    function save($tableName)
    {        
        if ($this->id == NULL){
            $query="INSERT INTO $tableName (name,deleted)
                                    VALUES(:name,0)";
        }else{
            $query="UPDATE $tableName SET name = :name
                    WHERE id = :id ";
        }
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':name', $this->name,PDO::PARAM_STR);
            if ($this->id != NULL){
                $stmt->bindValue(':id',    $this->id,   PDO::PARAM_INT);
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
    function deleteEntry($id,$tableName)
    {
        $query="UPDATE $tableName SET deleted = 1 WHERE id = :id ";
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':id', $id,   PDO::PARAM_INT);
            $stmt->execute();
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }

}    
$dir=new directoryEntry($dbh);    
switch ($method) {
case 'PUT':  
    $dir->checkAllowTabel($request[0]);
    $dir->checkRights("editDirectory");
    $dir->fromPostData();
    print($dir->Save($request[0]));    
    break;
  case 'GET':
    $dir->checkAllowTabel($request[0]); 
    $dir->checkRights("editDirectory");
    $dir->loadFromBd($request[1],$request[0]);
    print($dir->getEntry());
    break;
  case 'DELETE':
    $dir->checkAllowTabel($request[0]);  
    $dir->checkRights("editDirectory");
    print($dir->deleteEntry($request[1],$request[0]));
    break;    
  default:
    var_dump($request);  
    break;
}

?>
