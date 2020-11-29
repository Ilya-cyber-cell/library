<?php
require('./lib/config.php');
require('./lib/user.php');

$user=new UserClass($dbh,"ivanov",0);
print ($user->lastName);
print_r($user->getRights());
?>
