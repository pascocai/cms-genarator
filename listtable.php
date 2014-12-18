<?php
require_once "config/config.php";
//$link = mysql_connect($servername,$username,$password);
$link = mysqli_connect($servername, $username,  $password, $c_dbName);
//$dbs = mysqli_query($link, $sql);
$dbName = $_POST['dbname'];
//$db_table = mysql_list_tables($dbName);
$sql = 'SHOW TABLES FROM '.$dbName;
$db_table = mysqli_query($link, $sql);
$tables = array();
while($row = mysqli_fetch_row($db_table)){
	$tables[] =$row[0];
}
echo json_encode($tables);				
?>