<?php
require('./config.php');
session_set_cookie_params(['path' => '/','samesite' => 'Lax']);
session_name('Private'); 
session_start(); 
$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
$reportName=$request[0];
if (!isset($_SESSION['userId'])){
    print ("Вы не авторизованы");
    die(0);
}
$notAccess = true;
if (isset($_SESSION['rights'])){
    foreach ($_SESSION['rights'] as $item) {
        if ( $item == 'report'){
            $notAccess = false;
        }
    }
}
if ($notAccess and $reportName != "mybook"){
    print ("У вас нет прав");
    die(0);
}
function renderTemplate($template, $param = false) {
  ob_start();
  if ($param) {
    extract($param);
  }
  include($template);
  $ret = ob_get_contents();
  ob_end_clean();
  return $ret;
}
if (file_exists ('./templates/'.$reportName.'_getdata.php' )){
    require('./templates/'.$reportName.'_getdata.php');
    $layout_content = renderTemplate('./templates/'.$reportName.'_template.php',['title' => $title,'tableContent' => $tableContent]);

    print($layout_content);
}

?>
