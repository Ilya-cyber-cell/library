<?php
require('./apiClass.php');

class CreatorsClass extends APIClass{
        function searchCreators($patern){
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare("SELECT creatorId, LastName, FirstName, midleName FROM creators WHERE LastName LIKE  concat(:LastName,'%') and deleted = 0 ORDER BY LastName,FirstName");
            $stmt->bindValue(':LastName', $patern, PDO::PARAM_STR);
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                $data[] = array("name" => $row['LastName']." ".$row['FirstName'],"id" =>  $row['creatorId'] );
            }
            $stmt = null;
            return $this->toJson(0,$data);
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    }
    function getFirstLetter(){
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare("SELECT DISTINCT SUBSTRING(LastName,1,1) as Letter FROM creators ORDER BY Letter");
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                if ($row['Letter'] != ""){
                    $data[] = $row['Letter'];
                };
            }
            $stmt = null;
            return $this->toJson(0,$data);
        } catch (PDOException $e) {
            return $this->toJson(1,$e->getMessage());
        }
    } 



}

$creators=new CreatorsClass($dbh);
switch ($method) {
  case 'GET':
    if ($request[0] == ""){
        print($creators->getFirstLetter());
    }else{
        print($creators->searchCreators($request[0]));
    };
    break;
  default:
    var_dump($request);  
    break;
}




?>
