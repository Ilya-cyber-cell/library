<?php
class UserClass
{
    public $login=NULL;
    public $lastName=NULL;
    public $firstName=NULL;
    public $midleName=NULL;
    public $roleId=NULL;
    public $roleName=NULL;
    public $userId=NULL;
    public $rights=NULL;
    private $dbh;
    function __construct($dbh,$login,$byId=0) {
        $this->dbh = $dbh;
        try{
            if ($byId ==0){
                $sth = $dbh->prepare("SELECT userId,login, lastName, firstName, midleName, users.roleId as roleId, roles.name as roleName
                                        FROM users
                                        LEFT JOIN roles ON roles.roleId = users.roleId
                                        WHERE login = :login and deleted = 0");
                $sth->bindValue(':login', $login, PDO::PARAM_STR);
            }else{
                $sth = $dbh->prepare("SELECT userId,login, lastName, firstName, midleName, users.roleId as roleId, roles.name as roleName
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
            $sth = null;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
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
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    function save()
    {
        if ($this->userId == NULL){
            print("new");
        }else{
            print("edit");
        }
        try{
#            $dbh = $this->dbh;
#            $stmt = $dbh->prepare($query);
#            $stmt->bindValue(':roleId', $roleId, PDO::PARAM_INT);
#            $stmt->execute();
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}

?>
