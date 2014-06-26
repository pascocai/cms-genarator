<?php
$link = mysql_connect('localhost','pasco','123456');
$dbName = $_POST['dbname'];
$db_table = mysql_list_tables($dbName);
$tables = array();
while($row = mysql_fetch_row($db_table)){
	$tables[] =$row[0];
}
echo json_encode($tables);				
?>