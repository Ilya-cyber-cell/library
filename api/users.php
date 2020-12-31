<?php
require('./apiClass.php');

class UsersClass extends APIClass{
    function getUsers(){
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare("SELECT userid,login, lastName, firstName, midleName, roles.name as roleName
                                   FROM users
                                   LEFT JOIN roles on roles.roleid = users.roleid
                                   WHERE deleted = 0");
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                $data[] = $row;
            }
            $stmt = null;
            return $this->toJson(0, $data);
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    } 
    function getTree(){
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare("SELECT userid as id,CONCAT(lastName, ' ',firstName, ' ', midleName) as label
                                   FROM users
                                   WHERE deleted = 0
                                   ORDER BY label");
            $stmt->execute();
            $tree=[];
            $treebranch=[];
            $oldLeter = "";
            $leter="";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                $leter = mb_substr($row['label'],0,1);
                if($oldLeter == ""){
                     $oldLeter = $leter;
                }
                if($oldLeter == $leter){
                    $treebranch[] = $row;
                }else{
                    $tree[] = array('id'=>$oldLeter,'label'=>$oldLeter, 'children'=>$treebranch);
                    $oldLeter = $leter;
                    $treebranch=[];
                    $treebranch[] = $row;
                }
            }
            $tree[] = array('id'=>$oldLeter,'label'=>$oldLeter, 'children'=>$treebranch);            
            $stmt = null;
            return $this->toJson(0,$tree);
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    } 
}

$users=new UsersClass($dbh);
switch ($method) {
  case 'GET':
    if($request[0] == 'tree'){
        $users->checkRights("editBook");
        print($users->getTree());
    }else{
        $users->checkRights("userManagement");
        print($users->getUsers());
    }
    break;
  default:
    var_dump($request);  
    break;
}




?>
