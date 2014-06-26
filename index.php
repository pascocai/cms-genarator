<?php
require_once "config/config.php";
$conn = MySQL_connect($servername, $username, $password);
MySQL_query('SET NAMES utf8');

/****************************************
 *								        *
 *          各种字段类型的选项          *
 *								        *
 ****************************************/
function loadtypes($fieldName, $addOrEdit) {
	if($addOrEdit=='edit')
		$optionName = 'edit_'.$fieldName;
	else {
		$optionName = $fieldName;
		$isCheck = 'checked';
	}
	echo '<tr class="fieldslist"><td class="tdstyle">'.$fieldName.' :</td><td class="tdstyle"><input type="checkbox" '.$isCheck.' id="'.$optionName.'_show_fields" name="show_fields[]" value="'.$fieldName.'" onclick="changeCheck(\''.$addOrEdit.'\')">
			<select id="'.$optionName.'_type" name="'.$fieldName.'_type" onchange="changeType(\''.$fieldName.'\',\''.$addOrEdit.'\');" style="width:80px">
				<option id="'.$optionName.'_type_v0" value="0">readonly</option>
				<option id="'.$optionName.'_type_v1" value="1">text</option>
				<option id="'.$optionName.'_type_v2" value="2">select</option>
				<option id="'.$optionName.'_type_v3" value="3">checkbox</option>
				<option id="'.$optionName.'_type_v4" value="4">radio</option>
				<option id="'.$optionName.'_type_v5" value="5">datepicker</option>
				<option id="'.$optionName.'_type_v6" value="6">textarea</option>
				<option id="'.$optionName.'_type_v7" value="7">upload photo</option>
				<option id="'.$optionName.'_type_v8" value="8">foreign key</option>
				<option id="'.$optionName.'_type_v9" value="9">hidden</option>
				<option id="'.$optionName.'_type_v10" value="10">parent table</option>
			</select>
			</td><td id="'.$optionName.'_options"></td></tr>';
}

function gencms($dbName, $tableName, $showFields) {
	require_once "gen.php";
	global $c_cmsPath, $c_configPath, $c_configPageName, $c_commonPageName, $filePath, $c_libsPath, $c_cssPath, $c_jsPath, $c_listPageName, $c_addPageName, $c_editPageName, $c_deletePageName, $c_loginPageName, $c_typeTableName, $c_optionTableName, $c_fkTableName, $conn;
	MySQL_query("use ".$dbName, $conn);
	$sql = "SELECT * FROM " . $tableName;
	$result = mysql_query($sql);
	$fields = mysql_num_fields($result);
	$rows   = mysql_num_rows($result);
	$fieldNames = array();
	for ($i=0; $i < $fields; $i++) {
		$fieldNames[$i]  = mysql_field_name($result, $i);
	}
	
	/* 生成存放cms文件的目录 */
	if(!is_dir('cms'))
		mkdir('cms');
	if(!is_dir($c_cmsPath.$tableName.'/'))
		mkdir($c_cmsPath.$tableName.'/');
	if(!is_dir($c_cmsPath.$tableName.'/'.$c_jsPath))
		mkdir($c_cmsPath.$tableName.'/'.$c_jsPath);
	$filePath = $c_cmsPath.$tableName.'/';
echo $c_cmsPath.$tableName;
	/* 设置各个变量 */
	if(is_dir($c_cmsPath.$tableName.'/')&&is_dir($c_cmsPath.$tableName.'/'.$c_jsPath)) {
		$gencms = new gencms();
		$gencms->typeTableName = $c_typeTableName;
		$gencms->optionTableName = $c_optionTableName;
		$gencms->fkTableName = $c_fkTableName;
		$gencms->configPath = $c_configPath;
		$gencms->configPageName = $c_configPageName;
		$gencms->commonPageName = $c_commonPageName;
		$gencms->filePath = $filePath;
		$gencms->libsPath = $c_libsPath;
		$gencms->cssPath = $c_cssPath;
		$gencms->jsPath = $c_jsPath;
		$gencms->listPageName = $c_listPageName;
		$gencms->addPageName = $c_addPageName;
		$gencms->editPageName = $c_editPageName;
		$gencms->deletePageName = $c_deletePageName;
		$gencms->loginPageName = $c_loginPageName;
	} else
		echo '目录未生成，请再试一次';
	
	echo '<h2>已生成页面：</h2>';
	
	/* 生成list.php页面 */
	$r = $gencms->genListPage($tableName, $fieldNames, $showFields);
	if($r!=false)
		echo 'list page: <a href="'.$filePath.$c_listPageName.'" target="_blank">'.$c_listPageName.
	'</a>';
	
	/* 生成add.php页面 */
	$gencms->genAddPage($tableName, $fieldNames, $showFields);
	if($r!=false)
		echo '<br><br>add page: <a href="'.$filePath.$c_addPageName.'" target="_blank">'.$c_addPageName.
	'</a>';
	
	/* 生成edit.php页面 */
	$gencms->genEditPage($tableName, $fieldNames);
	if($r!=false)
		echo '<br><br>edit page: <a href="'.$filePath.$c_editPageName.'" target="_blank">'.$c_editPageName.'</a>';
		
	/* 生成delete.php页面 */
	$gencms->genDeletePage($tableName, $fieldNames);
	if($r!=false)
		echo '<br><br>edit page: '.$c_deletePageName;
	
	/* 生成list.js页面 */
	$gencms->genListJs($fieldNames);
	if($r!=false)
		echo '<br><br>list.js page: <a href="'.$filePath.$c_jsPath.'list.js" target="_blank">list.js</a>';
		
	/* 生成login.php页面 */
	$gencms->genLoginPage();
	if($r!=false)
		echo '<br><br>login page: <a href="'.$filePath.$c_loginPageName.'" target="_blank">'.$c_loginPageName.'</a>';
	
	echo '<br><br><h3>请根据需要手工添加config、js、css、lib文件和upload目录</h3>';
	mysql_free_result($result);
	mysql_close();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>CMS Generator v1.0</title>
		<link type="text/css" href="css/style.css" rel="stylesheet">
		<script type="text/javascript" src="js/index.js"></script>
		<script type="text/javascript" src="libs/jquery-1.4.2.min.js"></script>
		<?php
			if($_GET['type']=='edit')
				echo '<script>var currentTab = 1;</script>';
			else
				echo '<script>var currentTab = 0;</script>';
		?>
	</head>
	<body>
	<div id="wrapper">
		<h1>CMS Generator v1.0</h1>
		<div id="tabContainer">
			<div id="tabs">
				<ul>
					<li id="tabHeader_0" onclick="changePage(0,1)">新增字段类型和选项</li>
					<li id="tabHeader_1" onclick="changePage(1,0)">从数据库读取字段类型和选项</li>
				</ul>
			</div>
			<div id="tabscontent">
				<div class="tabpage" id="tabpage_0">
			<?php
				$dbName = $_POST['dbname'];
				$tableName = $_POST['tablename'];
				$showFields = $_POST['show_fields'];
				if($_GET['action']=='gen' && $_GET['type']=='add') {
					gencms($dbName, $tableName, $showFields);
				} else {
					$dbs = mysql_list_dbs($conn);
					$tableName = $_POST['tablename'];
					if($tableName=='') {
						echo '<h2>1.请选择数据库和表：</h2>
						<form id="" name="" action="'.$c_indexPageName.'?type=add" method="post" enctype="multipart/form-data"><select id="dbname" name="dbname" onchange="changeDB(\'\');"><option>请选择db</option>';
						while($row = mysql_fetch_object($dbs)){
							$db_name = $row->Database;
							echo '<option>'.$db_name.'</option>';
						}
						echo '</select><select id="tablename" name="tablename"><option value="">--------</option></select><input value="确定" type="submit" /></form>';
					} else {
						$dbName = $_POST['dbname'];
						MySQL_query("use ".$dbName, $conn);
						$sql = "SELECT * FROM " . $tableName;
						$result = mysql_query($sql);
						$fields = mysql_num_fields($result);
						$table  = mysql_field_table($result, 0);
						if($table) {
							$fieldNames = array();
							// 循环load出数据库字段，需要确保第一个是自增id，记录新增/更新时间默认用create_time/$update_time 
							echo '<h2>请选择字段类型或选项：</h2>
								<form id="typeForm" name="typeForm" method="post" action="'.$c_indexPageName.'?action=gen&type=add">
									<input type="hidden" id="dbname" name="dbname" value="'.$dbName.'"><input type="hidden" id="tablename" name="tablename" value="'.$tableName.'"><table><tbody><tr class="fieldslist"><td class="tdstyle">全选</td><td class=""><input type="checkbox" checked id="add_all_check" onclick="allCheck(\'add\')"></td></tr>';
							for ($i=0; $i < $fields; $i++) {
								//$type  = mysql_field_type($result, $i);
								//$len   = mysql_field_len($result, $i);
								//$flags = mysql_field_flags($result, $i);
								$fieldNames[$i]  = mysql_field_name($result, $i);
								loadtypes($fieldNames[$i], '');
							}
							echo '<tr><td><br><input value="GEN" type="submit" /></td><td></td></tr></tbody></table></form>';
						} else {
							echo '<h2>找不到该表，请正确选择：</h2>
						<form id="" name="" action="'.$c_indexPageName.'?type=add" method="post" enctype="multipart/form-data"><select id="dbname" name="dbname" onchange="changeDB(\'\');"><option>请选择db</option>';
						while($row = mysql_fetch_object($dbs)){
							$db_name = $row->Database;
							echo '<option>'.$db_name.'</option>';
						}
						echo '</select><select id="tablename" name="tablename"><option value="">--------</option></select><input value="确定" type="submit" /></form>';
						}
					}
				}
			?>
				</div>
				<div class="tabpage" id="tabpage_1">
			<?php
				$dbName = $_POST['dbname'];
				$tableName = $_POST['tablename'];
				$showFields = $_POST['show_fields'];
				if($_GET['action']=='gen' && $_GET['type']=='edit') {
					gencms($dbName, $tableName, $showFields);
				} else {
					$dbs = mysql_list_dbs($conn);
					$tableName = $_POST['tablename'];
					if($tableName=='') {
						echo '<h2>2.请选择数据库和表：</h2>
						<form id="" name="" action="'.$c_indexPageName.'?type=edit" method="post" enctype="multipart/form-data"><select id="edit_dbname" name="dbname" onchange="changeDB(\'edit_\');"><option>请选择db</option>';
						while($row = mysql_fetch_object($dbs)){
							$db_name = $row->Database;
							echo '<option>'.$db_name.'</option>';
						}
						echo '</select><select id="edit_tablename" name="tablename"><option value="">--------</option></select><input value="确定" type="submit" /></form>';
					} else {
						$dbName = $_POST['dbname'];
						MySQL_query("use ".$dbName, $conn);
						$sql = "SELECT * FROM " . $tableName;
						$result = mysql_query($sql);
						$fields = mysql_num_fields($result);
						$table  = mysql_field_table($result, 0);
						if($table) {
							$fieldNames = array();
							// 循环load出数据库字段，需要确保第一个是自增id，记录新增/更新时间默认用create_time/update_time
							echo '<h2>请修改字段类型或选项：</h2>
								<form id="typeForm" name="typeForm" method="post" action="'.$c_indexPageName.'?action=gen&type=edit">
									<input type="hidden" id="dbname" name="dbname" value="'.$dbName.'"><input type="hidden" id="tablename" name="tablename" value="'.$tableName.'"><table><tbody><tr class="fieldslist"><td class="tdstyle">全选</td><td class=""><input type="checkbox" id="edit_all_check" onclick="allCheck(\'edit\')"></td></tr>';
							for ($i=0; $i < $fields; $i++) {
								$fieldNames[$i] = mysql_field_name($result, $i);
								// 读取该字段的类型
								$sql = "SELECT * FROM ".$c_typeTableName." WHERE table_field = '".$tableName.'_'.$fieldNames[$i]."'";
								$rs = mysql_query($sql);
								$type = mysql_fetch_array($rs);
								loadtypes($fieldNames[$i], 'edit');
								echo '<script>';
								echo 'document.getElementById("edit_'.$fieldNames[$i].'_type_v'.$type['type_value'].'").selected = true;';
								if($type['is_checked']==1)
									echo 'document.getElementById("edit_'.$fieldNames[$i].'_show_fields'.'").checked = true;';
								echo '</script>';
								if($type['type_value']>1&&$type['type_value']<5) {
									// 读取该字段的选项
									$sql = "SELECT * FROM ".$c_optionTableName." WHERE table_field = '".$tableName.'_'.$fieldNames[$i]."'";
									$rs = mysql_query($sql);
									$options = mysql_num_rows($rs);
									echo '<script>document.getElementById("edit_'.$fieldNames[$i].'_options").innerHTML = \'<input value="add" type="button" onclick="addOption(\\\''.$fieldNames[$i].'\\\',\\\'edit\\\')" /><input value="del" type="button" onclick="delOption(\\\''.$fieldNames[$i].'\\\',\\\'edit\\\')" /><input type="hidden" id="edit_'.$fieldNames[$i].'_options_count" name="'.$fieldNames[$i].'_options_count" value='.$options.'>';
									for($j=0;$j<$options;$j++) {
										$option = mysql_fetch_array($rs);
										echo '<div><input type="text" id="'.$fieldNames[$i].'_option_'.$j.'" name="'.$fieldNames[$i].'_option_'.$j.'" value="'.$option['option_value'].'"></div>';
									}
									echo '\';</script>';
								} elseif($type['type_value']==8) {
									// 读取该字段的选项
									$sql = "SELECT * FROM ".$c_fkTableName." WHERE table_field = '".$tableName.'_'.$fieldNames[$i]."'";
									$rs = mysql_query($sql);
									$options = mysql_num_rows($rs);
									echo '<script>document.getElementById("edit_'.$fieldNames[$i].'_options").innerHTML = \'<input type="hidden" id="edit_'.$fieldNames[$i].'_options_count" name="'.$fieldNames[$i].'_options_count" value='.$options.'>';
									for($j=0;$j<$options;$j++) {
										$option = mysql_fetch_array($rs);
										echo '<div><input type="text" id="'.$fieldNames[$i].'_option_'.$j.'" name="'.$fieldNames[$i].'_option_'.$j.'" value="'.$option['fk_table_name'].'"></div>';
									}
									echo '\';</script>';
								}
							}
							echo '<tr><td><br><input value="GEN" type="submit" /></td><td></td></tr></tbody></table></form>';
						} else {
							echo '<h2>找不到该表，请正确选择：</h2>
						<form id="" name="" action="'.$c_indexPageName.'?type=edit" method="post" enctype="multipart/form-data"><select id="edit_dbname" name="dbname" onchange="changeDB(\'edit_\');"><option>请选择db</option>';
						while($row = mysql_fetch_object($dbs)){
							$db_name = $row->Database;
							echo '<option>'.$db_name.'</option>';
						}
						echo '</select><select id="edit_tablename" name="tablename"><option value="">--------</option></select><input value="确定" type="submit" /></form>';
						}
					}
				}
			?>
				</div>
			</div>
			<a class="backlink" href="<?php echo $c_indexPageName; ?>">返回首页</a>
		</div>
	</body>
</html>
