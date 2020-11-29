<?php
require('./lib/config.php');
require('./lib/user.php');
require('./lib/book.php');

$user=new UserClass($dbh,"ivanov",0);
print ($user->lastName);
print_r($user->getRights());
print ("<br>");
$book=new BookClass($dbh,66541);
print_r($book);

?>
