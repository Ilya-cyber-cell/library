<?php
class SearchClass extends APIClass
{
    private $dbh;
        function __construct($dbh) {
            $this->dbh = $dbh;
        }
        function searchCreators($patern){
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare("SELECT creatorId, LastName, FirstName, midleName FROM creators WHERE LastName LIKE  concat(:LastName,'%') ORDER BY LastName,FirstName");
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
    function getBooks($creatorId){
        try{
            $dbh = $this->dbh;
            $stmt = $dbh->prepare("SELECT bookId, Title, description, creators.LastName as LastName, creators.FirstName as FirstName , creators.midleName as midleName
                                   FROM books
                                   LEFT JOIN creators on creators.creatorId = books.creatorId
                                   WHERE books.creatorId = :creatorId");
            $stmt->bindValue(':creatorId', $creatorId, PDO::PARAM_INT);
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
?>
