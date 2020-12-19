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
            return $this->toJson(0,$data);
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    } 

}

$users=new UsersClass($dbh);
switch ($method) {
  case 'GET':
        print($users->getUsers());
    break;
  default:
    var_dump($request);  
    break;
}




?>
