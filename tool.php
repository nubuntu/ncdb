<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include("ncdb.php");
//$ncdb=new NCDB;
/**
$arr = array(
	'secret'=>"12345",
	'root'=>"root"
);
echo $e =$ncdb->aesEncrypt("22222",json_encode($arr));
echo "<br/>";
file_put_contents("config.php", $e);
echo $ncdb->aesDecrypt("22222",$e);
**/
$arr = array(
	'username'=>"root",
	'password'=>"root"
);
$ncdb->write("user/root",$arr);
var_dump($ncdb->read("user/root"));