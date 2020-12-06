<?php
class BookClass extends APIClass
{
    public $bookId=NULL;
    public $Title=NULL;
    public $description=NULL;
    public $creatorLastName=NULL;
    public $creatorFirstName=NULL;
    public $creatorMidleName=NULL;
    public $publisher=NULL;
    public $language=NULL;
    public $identifier=NULL;
    public $genres=NULL;
    public $availability=NULL;
    private $dbh;
    function __construct($dbh,$bookId) {
        try{
            $this->dbh = $dbh;
            $sth = $dbh->prepare("SELECT bookId, Title, description,  publishers.name as publisher, languages.name as language, identifier, types.typeId,
                                    creators.LastName as LastName, creators.FirstName as FirstName , creators.midleName as midleName
                                    FROM books
                                    LEFT JOIN creators on creators.creatorId = books.creatorId
                                    LEFT JOIN publishers on publishers.publisherId = books.publisherId
                                    LEFT JOIN languages on languages.id = books.languageId
                                    LEFT JOIN types on types.typeId = books.typeId
                                    WHERE bookId = :bookId");
            $sth->bindValue(':bookId', $bookId, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            
            $this->bookId=$bookId; 
            $this->Title=$result['Title']; 
            $this->description=$result['description']; 
            $this->creatorLastName=$result['LastName']; 
            $this->creatorFirstName=$result['FirstName']; 
            $this->creatorMidleName=$result['midleName']; 
            $this->publisher=$result['publisher']; 
            $this->language=$result['language']; 
            $this->identifier=$result['identifier']; 
            $sth = null;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        $this->genres = $this->getGenres();
        $this->availability = $this->getAvailability();
    }
    function getGenres()
    {
        try{
            $dbh = $this->dbh;
            $bookId = $this->bookId;
            $stmt = $dbh->prepare("SELECT name FROM itemGenres 
                                    LEFT JOIN genres on itemGenres.genresId = genres.genresId
                                    WHERE bookId = :bookId");
            $stmt->bindValue(':bookId', $bookId, PDO::PARAM_INT);
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
    function getAvailability()
    {
        try{
            $dbh = $this->dbh;
            $bookId = $this->bookId;
            $stmt = $dbh->prepare("SELECT inventoryId,state,expireDate,location FROM inventory 
                                    WHERE bookId = :bookId");
            $stmt->bindValue(':bookId', $bookId, PDO::PARAM_INT);
            $stmt->execute();
            $data=[];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                $data[] = $row;
            }
            $stmt = null;
            return $data;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }    
    function getBook(){
        return $this->toJson(0,$this);
    }
}



?>
