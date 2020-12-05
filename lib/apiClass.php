<?php
class APIClass
{
    function toJson($errorCode,$data){
        $out =  array("error" => $errorCode,"content" => $data);
        return json_encode($out,JSON_UNESCAPED_UNICODE);
    }
}
require('./lib/search.php');
require('./lib/config.php');
require('./lib/user.php');
require('./lib/book.php');
?>
