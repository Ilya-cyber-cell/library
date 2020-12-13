<?php
class APIClass
{
    protected   $dbh;
    function __construct($dbh) {
         $this->dbh = $dbh;
    }
    function toJson($errorCode,$data){
        $out =  array("error" => $errorCode,"content" => $data);
        return json_encode($out,JSON_UNESCAPED_UNICODE);
    }
    function fromJson($json){
        foreach ($json as $key => $value) {
            $this->{$key}=$value;
        }
    }
}
require('./lib/search.php');
require('./lib/config.php');
require('./lib/user.php');
require('./lib/book.php');
?>
