<?php
require('./lib/apiClass.php');
session_set_cookie_params(['path' => '/','samesite' => 'Lax']);
session_name('Private'); 
session_start(); 

$request = json_decode($_POST['json']);
if($request->{'module'} == "Search" ){
    $search=new SearchClass($dbh);
    If($request->{'actons'} == 'getCreators'){
        print($search->searchCreators($request->{'patern'}));
    }elseif($request->{'actons'} == 'getFirstLetter'){
        print($search->getFirstLetter());
    }elseif($request->{'actons'} == 'getBooks'){
        print($search->getBooks($request->{'creatorId'}));
    }
}elseif($request->{'module'} == "User"){
    If($request->{'actons'} == 'login'){
        $user=new UserClass($dbh,$request->{'login'},0);
        print($user->checkPassword($request->{'password'}));
    }
}elseif($request->{'module'} == "Book"){
    $book=new BookClass($dbh,$request->{'bookId'});
    if($request->{'actons'} == 'getBook'){
        print($book->getBook());
    }
}
?>
