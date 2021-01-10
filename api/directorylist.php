<?php
require('./apiClass.php');

class directoryList extends directoryEditor{
    function getList($tableName){
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare("SELECT id,name FROM $tableName WHERE deleted = 0");
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

$list=new directoryList($dbh);
switch ($method) {
  case 'GET':
    $list->checkAllowTabel($request[0]);
    $list->checkRights("editDirectory");
    print($list->getList($request[0]));
    break;
  default:
    var_dump($request);  
    break;
}


?>
