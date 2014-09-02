<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include("ncdb.php");
$data = array(
	array("id"=>1,"nama"=>"noer"),
	array("id"=>2,"nama"=>"cholis"),
	array("id"=>3,"nama"=>"kendo"),
	array("id"=>4,"nama"=>"vibi")	
);
//mkdir("database/test");
//$ncdb->write("database/test/test",$data);
$before = microtime(true);
$data = $ncdb->read("database/test/test");
//var_dump($data);
$after = microtime(true);
echo $after-$before;
echo "<br/>";
$mysql=new mysqli("localhost","root","root","test");
$before = microtime(true);
$cur = $mysql->query("select * from test");
$data=array();
while ($row = $cur->fetch_object()) {
	$data[] = $row;
}
//var_dump($data);
$after = microtime(true);
echo $after-$before;

?>