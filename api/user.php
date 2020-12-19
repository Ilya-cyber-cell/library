<?php
require('./apiClass.php');
class UserClass extends APIClass
{
    public    $login=NULL;
    public    $lastName=NULL;
    public    $firstName=NULL;
    public    $midleName=NULL;
    public    $roleId=NULL;
    public    $roleName=NULL;
    public    $userId=NULL;
    public    $rights=NULL;
    public    $allRoles=NULL;
    private   $passHash;
    protected $password;
    function loadFromBd($login,$byId=0) {
        try{
            $dbh = $this->dbh;
            if ($byId ==0){
                $sth = $dbh->prepare("SELECT userId,login, lastName, firstName, midleName, users.roleId as roleId, roles.name as roleName, password
                                        FROM users
                                        LEFT JOIN roles ON roles.roleId = users.roleId
                                        WHERE login = :login and deleted = 0");
                $sth->bindValue(':login', $login, PDO::PARAM_STR);
            }else{
                $sth = $dbh->prepare("SELECT userId,login, lastName, firstName, midleName, users.roleId as roleId, roles.name as roleName, password
                                        FROM users
                                        LEFT JOIN roles ON roles.roleId = users.roleId
                                        WHERE userId = :userId");
                $sth->bindValue(':userId', $login, PDO::PARAM_INT);
            }
            
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            $this->userId=$result['userId']; 
            $this->login=$result['login']; 
            $this->lastName=$result['lastName']; 
            $this->firstName=$result['firstName']; 
            $this->midleName=$result['midleName']; 
            $this->roleId=$result['roleId']; 
            $this->roleName=$result['roleName']; 
            $this->passHash=$result['password']; 
            $sth = null;
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
        $this->rights=$this->getRights();
    }
    function getRights()    {
        try{
            $dbh = $this->dbh;
            $roleId = $this->roleId;
            $stmt = $dbh->prepare("SELECT rights FROM itemRights WHERE roleId = :roleId");
            $stmt->bindValue(':roleId', $roleId, PDO::PARAM_INT);
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $data[] = $row[0];
            }
            $stmt = null;
            return $data;
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
    function getAllRoles()    {
        try{
            $dbh = $this->dbh;
            $roleId = $this->roleId;
            $stmt = $dbh->prepare("SELECT roleid, name FROM roles");
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $data[] = array("name" => $row[1],"id" =>  $row[0] );
            }
            $stmt = null;
            return $data;
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }    
    function save()
    {
        if ($this->password == "" or $this->password == NULL){
            $password = "";
        }else{
            $password = ",password =:password";
        }
        if ($this->userId == NULL){
            $query="INSERT INTO users(login,lastName,firstName,midleName,roleId,password,deleted)
                                    VALUES(:login,:lastName,:firstName,:midleName,:roleId,:password,0)";
        }else{
            $query="UPDATE users SET login = :login, lastName = :lastName,firstName = :firstName, midleName = :midleName,roleId = :roleId  $password
                    WHERE userId = :userId ";
        }
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':roleId',    $this->roleId,   PDO::PARAM_INT);
            $stmt->bindValue(':login',     $this->login,    PDO::PARAM_STR);
            $stmt->bindValue(':lastName',  $this->lastName, PDO::PARAM_STR);
            $stmt->bindValue(':firstName', $this->firstName,PDO::PARAM_STR);
            $stmt->bindValue(':midleName', $this->midleName,PDO::PARAM_STR);
            if ($this->userId != NULL){
                $stmt->bindValue(':userId',    $this->userId,   PDO::PARAM_INT);
            }
            if($password != ""){
                $stmt->bindValue(':password',  password_hash($this->password,PASSWORD_BCRYPT),PDO::PARAM_STR);
            }
            $stmt->execute();
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
    function checkPassword($password){
        if (password_verify($password, $this->passHash)) {
            return $this->toJson(0,$this->rights);
            $_SESSION['rights']=$this->rights;
        } else {
            return $this->toJson(1,"Пароль неправильный.");
            unset ($_SESSION['rights']);
        }
    }
    function getUser($param=""){
        $allRoles="";
        if ($param =="allRoles"){
            $allRoles=$this->getAllRoles();
        }
        return $this->toJson(0,array("user"=>$this,"roles"=>$allRoles));
    }
    function deleteUser($userId)
    {
        $query="UPDATE users SET deleted = 1 WHERE userId = :userId ";
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':userId', $userId,   PDO::PARAM_INT);
            $stmt->execute();
            return $this->toJson(0,"ok");
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
}    
$user=new UserClass($dbh);    
switch ($method) {
case 'PUT':  
    $user->fromPostData();
    print($user->Save());    
    break;
  case 'POST':
    $request = json_decode($_POST['json']);
    If($request->{'actons'} == 'login'){
        $user->loadFromBd($request->{'login'},0);
        print($user->checkPassword($request->{'password'}));  
    }
    break;
  case 'GET':
    $user->loadFromBd($request[0],1);
    print($user->getUser("allRoles"));
    break;
  case 'DELETE':
    print($user->deleteUser($request[0]));
    break;    
  default:
    var_dump($request);  
    break;
}

?>
