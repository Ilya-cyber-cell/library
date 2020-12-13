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
    }elseif($request->{'actons'} == 'getUsers'){
        print($search->getUsers());
    }
}elseif($request->{'module'} == "User"){
        $user=new UserClass($dbh);
    If($request->{'actons'} == 'login'){
        $user->loadFromBd($request->{'login'},0);
        print($user->checkPassword($request->{'password'}));
    }elseif($request->{'actons'} == 'getUser'){
        $user->loadFromBd($request->{'userId'},1);
        print($user->getUser("allRoles"));
    }elseif($request->{'actons'} == 'save'){
        $user->fromJson($request->{'user'});
        print($user->Save());
    }
}elseif($request->{'module'} == "Book"){
    $book=new BookClass($dbh);
    if($request->{'actons'} == 'getBook'){
        $book->loadFromBd($request->{'bookId'});
        print($book->getBook());
    }elseif($request->{'actons'} == 'save'){
        $book->fromJson($request->{'book'});
        print($book->Save());
    }
}
?>
