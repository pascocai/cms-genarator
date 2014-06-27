<?php
require_once "config/config.php";
$link = mysql_connect($servername,$username,$password);
$dbName = $_POST['dbname'];
$db_table = mysql_list_tables($dbName);
$tables = array();
while($row = mysql_fetch_row($db_table)){
	$tables[] =$row[0];
}
echo json_encode($tables);				
?>