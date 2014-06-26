<?php
class gencms {
	var $typeTableName;
	var $optionTableName;
	var $fkTableName;
	var $indexName;
	var $configPath;
	var $filePath;
	var $libsPath;
	var $cssPath;
	var $jsPath;
	var $configPageName;
	var $commonPageName;
	var $listPageName;
	var $addPageName;
	var $editPageName;
	var $deletePageName;
	var $loginPageName;

	/*************************************
	 *								     *
	 *			生成list.php页面		 *
	 *								     *
	 *************************************/
	public function genListPage($tableName, $fieldNames, $showFields) {
		$fields = count($fieldNames);
		$hasFK = 'false';
		for ($i=0; $i < $fields; $i++) {
			if($fieldNames[$i]=='fkid'){
				$hasFK = 'true';
			}
		}
		$listCode .= '<?php
	require_once \'../'.$this->configPath.$this->configPageName.'\';
	$conn = MySQL_connect($servername, $username, $password);
	MySQL_query(\'use \'.$dbname, $conn);
	MySQL_query(\'SET NAMES utf8\');
	require_once \'../'.$this->configPath.$this->commonPageName.'\';
	require_once \''.$this->libsPath.'shareFunction.php\';
	$currentTime = date(\'Y-m-d H:i:s\');
	$mpurl = \''.$this->listPageName .'\';
	$tpp = 10;
	if(\'\'!=trim($_GET[\'page\'])) {
		$page = checkParam(trim($_GET[\'page\']), true);
		$startLimit = ($page - 1) * $tpp;
	} else {
		$startLimit = 0;
		$page = 1;
	}
	$fkid = 0;
	$hasFK = '.$hasFK.';
	if($hasFK)
		$fkid = $_GET[\'fkid\'];
	$sql = "SELECT count(*) FROM '.$tableName.' WHERE '.$fieldNames[$fields-1].' = 0";
	$query = MySQL_query($sql);
	$rt = mysql_fetch_array($query);
	$rscount = $rt[0];
	$pagination = multiAdmin($rscount, $tpp, $page, $mpurl, \'fkid=\'.$fkid);
	if($hasFK)
		$rs = MySQL_query("SELECT * FROM '.$tableName.' WHERE '.$fieldNames[$fields-1].' = 0 AND fkid = ". $fkid ." ORDER BY '.$fieldNames[0].' DESC limit $startLimit, $tpp");
	else
		$rs = MySQL_query("SELECT * FROM '.$tableName.' WHERE '.$fieldNames[$fields-1].' = 0 ORDER BY '.$fieldNames[0].' DESC limit $startLimit, $tpp");
	$fields = mysql_num_fields($rs);
	$rows = mysql_num_rows($rs);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>list of '.$tableName.'</title>
		<link href="'.$this->cssPath.'style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="'.$this->libsPath.'jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="'.$this->jsPath.'list.js"></script>
	</head>
	<body>
		<center>
			<nav>
				<ul class="fancyNav" style="">
					<li id="toCate"><a href="#">列表</a></li>
					<li id="logout"><a href="login.php?action=logout">logout</a></li>
				</ul>
			</nav>
		</center>
		<div class="fixwidth">
			<?php //echo \''.$tableName.'\' . \' has \' . $fields . \' fields and \' . $rscount . \' record(s)<br><br>\'; ?>
			';
		if($hasFK)
			$listCode .= '<input type="hidden" id="hasFK" value="<?php echo $fkid; ?>">
			<?php
			session_start();
			if($_SESSION[\'rights\']<=2) {
			?>
			<button class="addButton" onclick="addRecord(\'<?php echo $page; ?>\',\'<?php $uri = request_uri();echo $uri; ?>\')" type="button">新增</button>
			<?php
			}
			?>
			';
		$listCode .= '<?php echo $pagination; ?>
			<table class="bordered">
				<tr>';
			// 循环load出数据库字段，需要确保第一个是自增id，如果类型=9则不在list中显示
			$hiddenArray = array();
			$fkArray = array();
			$fkTableArray = array();
			$imgArray = array();
			for ($i=0; $i < $fields; $i++) {
				if($_POST[$fieldNames[$i].'_type']==7) {
					$listCode .= '<th>'.$fieldNames[$i].'</th>';
					$imgArray[$i] = $i;
				}elseif($_POST[$fieldNames[$i].'_type']==8){
					$fkArray[$i] = $i;
					$listCode .= '<th>'.$fieldNames[$i].'</th>';
					$fkTableArray[$i] = '\''.$_POST[$fieldNames[$i].'_option_0'].'\'';
				} elseif($_POST[$fieldNames[$i].'_type']==9)
					$hiddenArray[$i] = $i;
				else {
					$match = 0;
					for ($j=0; $j < count($showFields); $j++) {
						if($showFields[$j]==$fieldNames[$i]) {
							$listCode .= '<th>'.$fieldNames[$i].'</th>';
							$match = 1;
						}
					}
					if($match==0)
						$hiddenArray[$i] = $i;
				}
			}
			$hiddenFields = implode(',',$hiddenArray);
			$fkFields = implode(',',$fkArray);
			$fkTables = implode(',',$fkTableArray);
			$imgFields = implode(',',$imgArray);
			$listCode .= '<th>操作</th></tr>
			<?php
			if($rows) {
				$hiddenFields = array('.$hiddenFields.');
				$fkFields = array('.$fkFields.');
				$fkTables = array('.$fkTables.');
				$imgFields = array('.$imgFields.');
				';
			$listCode .= 'while($row = mysql_fetch_array($rs)){
					echo \'<tr>\';
					$fkTableIndex = 0;
					for($i=0;$i<$fields;$i++){
						if(!in_array($i, $hiddenFields)) {
							if(in_array($i, $fkFields)) {
								echo \'<td><a target="_blank" href="../\'.$fkTables[$fkTableIndex].\'/'.$this->listPageName.'?fkid=\'.$row[0].\'">\'.$fkTables[$fkTableIndex].\'</a></td>\';
								$fkTableIndex++;
							} elseif(in_array($i, $imgFields)) {
								echo \'<td><div class="imgdiv"><img class="thumbimg" src="\'.$row[$i].\'"></div></td>\';
							}else
								echo \'<td>\'.$row[$i].\'</td>\';
						}
					}
					if($_SESSION[\'rights\']<=2)
						echo \'<td><button class="modButton" onclick="editRecord(\\\''.$fieldNames[0].'=\'.$row[0].\'&page=\'.$page.\'&url=\'.$uri.\'\\\')" type="button">修改</button><button class="modButton" onclick="javascript:deleteRecord(\'.$row[0].\')" type="button">刪除</button></td></tr>\';
					else
						echo \'<td></td></tr>\';
				}
			} else
				echo \'<tr><td colspan="'.($fields+1).'">暂无资料</td></tr>\';
			mysql_free_result($rs);
			mysql_close($conn);
			?>
			</table>
			<?php echo $pagination; ?>
		</div>
	</body>
</html>';
		$fh = fopen($this->filePath.$this->listPageName, 'w');
		$r = fwrite($fh, $listCode);
		fclose($fh);
		return $r;
	}

	/************************************
	 *								    *
	 *			生成add.php页面			*
	 *								    *
	 ************************************/
	public function genAddPage($tableName, $fieldNames, $showFields) {
		require_once 'config/config.php';
		$fields = count($fieldNames);
		$addCode .= '<?php
	require_once \'../'.$this->configPath.$this->commonPageName.'\';
	session_start();
	if($_SESSION[\'rights\']>2) {
		$url = base64_decode($_GET[\'url\']);
		header(\'Location: \'.$url);
	}	
	$page = $_GET[\'page\'];
	if($_GET[\'action\']==\'add\') {
		require_once \'../'.$this->configPath.$this->configPageName.'\';
		$conn = MySQL_connect($servername, $username, $password);
		MySQL_query(\'use \'.$dbname, $conn);
		MySQL_query(\'SET NAMES utf8\');
		require_once \''.$this->libsPath.'shareFunction.php\';';
		for ($i=0; $i < $fields; $i++) {
			if($_POST[$fieldNames[$i].'_type']==3) {
				$addCode .= '
		$'.$fieldNames[$i].' = $_POST[\''.$fieldNames[$i].'\'];
		$'.$fieldNames[$i].' = implode(\',\',$'.$fieldNames[$i].');';
			} elseif($_POST[$fieldNames[$i].'_type']==7) {
				$addCode .= '
		$'.$fieldNames[$i].' = getFilePath($_FILES[\''.$fieldNames[$i].'\']);
		if($'.$fieldNames[$i].'>=1)
			$'.$fieldNames[$i].' = \'\';';
			} elseif($_POST[$fieldNames[$i].'_type']!=9)
				$addCode .= '
		$'.$fieldNames[$i].' = $_POST[\''.$fieldNames[$i].'\'];';
		}
		$addCode .= '
		$create_time = date(\'Y-m-d H:i:s\');
		$update_time = date(\'Y-m-d H:i:s\');
		$query = MySQL_query("INSERT INTO '.$tableName;
		$addCode .= ' (';
		for ($i=0; $i < $fields; $i++) {
			if($i==0)
				$addCode .= $fieldNames[$i];
			else
				$addCode .= ', '.$fieldNames[$i];
		}
		$addCode .= ') VALUES (';
		for ($i=0; $i < $fields; $i++) {
			if($i==0)
				$addCode .= '\'$'.$fieldNames[$i].'\'';
			else
				$addCode .= ', \'$'.$fieldNames[$i].'\'';
		}
		$addCode .= ')");
		if($query==1 && isset($_GET[\'fkid\']))
			header(\'Location: '.$this->listPageName.'?page=\'.$page.\'&fkid=\'.$_GET[\'fkid\']);
		else
			header(\'Location: '.$this->listPageName.'?page=\'.$page);
	} else
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>add '.$tableName.'</title>
		<link type="text/css" rel="stylesheet" href="'.$this->cssPath.'style.css">
		<script language="javascript" type="text/javascript" src="'.$this->libsPath.'datepicker/WdatePicker.js"></script>
	</head>
	<body class="theme3">
		<div>
			<fieldset id="addFormHeader">
				<input id="back" type="button" onclick="location.href=\''.$this->listPageName.'?page=<?php echo $page;if(isset($_GET[\'fkid\']))echo \'&fkid=\'.$_GET[\'fkid\']; ?>\'" value="返回列表" name="back">
				<h1>新增</h1>
			</fieldset>
			<form id="addForm" name="addForm" action="'.$this->addPageName.'?action=add&page=<?php echo $page;if(isset($_GET[\'fkid\']))echo \'&fkid=\'.$_GET[\'fkid\']; ?>" method="post" enctype="multipart/form-data">
				<fieldset id="addFormBody">';
		$sql = "DELETE FROM ".$this->typeTableName." WHERE table_name = '".$tableName."'";
		MySQL_query($sql);
		$sql = "DELETE FROM ".$this->fkTableName." WHERE table_name = '".$tableName."'";
		MySQL_query($sql);
		$sql = "DELETE FROM ".$this->optionTableName." WHERE table_name = '".$tableName."'";
		MySQL_query($sql);
		for ($i=0; $i < $fields; $i++) {
			if($_POST[$fieldNames[$i].'_type']==0) {	// 0 = 只读
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label><span class="readonly"><span class="text"></span></span>
					</div>';
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
			} elseif($_POST[$fieldNames[$i].'_type']==1) {	// 1 = text输入框
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<input type="text" id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'">
					</div>';
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
			} elseif($_POST[$fieldNames[$i].'_type']==2) {	// 2 = 下拉菜单，生成代码后，第一个选项v0的值会为空
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<span class="readonly">
							<select id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'">';
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
				for ($j=0; $j < $_POST[$fieldNames[$i].'_options_count']; $j++) {
					if($j==0)
						$addCode .= '
								<option id="'.$fieldNames[$i].'_v'.$j.'" value="">'.$_POST[$fieldNames[$i].'_option_'.$j].'</option>';
					else
						$addCode .= '
								<option id="'.$fieldNames[$i].'_v'.$j.'" value="'.$_POST[$fieldNames[$i].'_option_'.$j].'">'.$_POST[$fieldNames[$i].'_option_'.$j].'</option>';
					// 将该字段的各个选项记入到数据库中
					$sql = "INSERT INTO ".$this->optionTableName." (table_name, table_field, option_index, option_value) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$j."', '".$_POST[$fieldNames[$i]."_option_".$j]."')";
					MySQL_query($sql);
				}
				$addCode .= '
							</select>
						</span>
					</div>';
			} elseif($_POST[$fieldNames[$i].'_type']==3) {	// 3 = 多项选择
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<span class="readonly">';	
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
				for ($j=0; $j < $_POST[$fieldNames[$i].'_options_count']; $j++) {			
					$addCode .= '
							<input type="checkbox" id="'.$fieldNames[$i].'_v'.$j.'" name="'.$fieldNames[$i].'[]" value="'.$_POST[$fieldNames[$i].'_option_'.$j].'">'.$_POST[$fieldNames[$i].'_option_'.$j];
					// 将该字段的各个选项记入到数据库中
					$sql = "INSERT INTO ".$this->optionTableName." (table_name, table_field, option_index, option_value) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$j."', '".$_POST[$fieldNames[$i]."_option_".$j]."')";
					MySQL_query($sql);
				}
				$addCode .= '
						</span>
					</div>';
			} elseif($_POST[$fieldNames[$i].'_type']==4) {	// 4 = 单项选择
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<span class="readonly">';
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
				for ($j=0; $j < $_POST[$fieldNames[$i].'_options_count']; $j++) {			
					$addCode .= '
							<input type="radio" id="'.$fieldNames[$i].'_v'.$j.'" name="'.$fieldNames[$i].'" value="'.$_POST[$fieldNames[$i].'_option_'.$j].'">'.$_POST[$fieldNames[$i].'_option_'.$j];
					// 将该字段的各个选项记入到数据库中
					$sql = "INSERT INTO ".$this->optionTableName." (table_name, table_field, option_index, option_value) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$j."', '".$_POST[$fieldNames[$i]."_option_".$j]."')";
					MySQL_query($sql);
				}
				$addCode .= '
						</span>
					</div>';
			} elseif($_POST[$fieldNames[$i].'_type']==5) {	// 5 = 日期控件
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<input id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'" class="Wdate" type="text" onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})">
						<img onclick="WdatePicker({el:\''.$fieldNames[$i].'\', dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" src="'.$this->cssPath.'datePicker.png" width="32px" height="32px" align="absmiddle">
					</div>';
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
			} elseif($_POST[$fieldNames[$i].'_type']==6) {	// 6 = textarea文本输入框
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<textarea cols="50" rows="10" id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'"></textarea>
					</div>';
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
			} elseif($_POST[$fieldNames[$i].'_type']==7) {	// 7 = 上传图片
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<input type="file" id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'">
					</div>';
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
			} elseif($_POST[$fieldNames[$i].'_type']==8) {	// 8 = 外键
				$fkTable = $_POST[$fieldNames[$i].'_option_0'];
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<span class="readonly">
							<a target="_blank" href="../'.$fkTable.'/'.$this->listPageName.'?fkid=<?php echo $result[0]; ?>">'.$fkTable.'</a>
						</span>
					</div>';
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
				for ($j=0; $j < $_POST[$fieldNames[$i].'_options_count']; $j++) {
					// 将该字段的各个选项记入到数据库中
					$sql = "INSERT INTO ".$this->fkTableName." (table_name, table_field, fk_table_name) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$fkTable."')";
					MySQL_query($sql);
				}
			} elseif($_POST[$fieldNames[$i].'_type']==9) {	// 隐藏字段，不显示，不能修改或由系统自动修改，一般记录用户新增/更新时间create_time/update_time
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
			} elseif($_POST[$fieldNames[$i].'_type']==10) {	// 隐藏字段，记录父表id
				$addCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<span class="readonly">
							<span class="text"><?php echo $_GET[\'fkid\']; ?></span>
						</span>
						<input type="hidden" id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'" value="<?php echo $_GET[\'fkid\']; ?>">
					</div>';
				// 将字段的类型记入到数据库中
				if(in_array($fieldNames[$i], $showFields))
					$isChecked = 1;
				else
					$isChecked = 0;
				$sql = "INSERT INTO ".$this->typeTableName." (table_name, table_field, type_value, is_checked) VALUES ('".$tableName."', '".$tableName."_".$fieldNames[$i]."', '".$_POST[$fieldNames[$i].'_type']."',".$isChecked.")";
				MySQL_query($sql);
			}
		} 
		$addCode .= '
				</fieldset>
				<fieldset id="addFormFooter">
					<input value="保存" type="submit" />
				</fieldset>
			</form>
		</div>
	</body>
</html>';
		$fh = fopen($this->filePath.$this->addPageName, 'w');
		$r = fwrite($fh, $addCode);
		fclose($fh);
		return $r;
	}

	/*************************************
	 *								     *
	 *			生成edit.php页面		 *
	 *								     *
	 *************************************/
	public function genEditPage($tableName, $fieldNames) {
		$fields = count($fieldNames);
		$editCode .= '<?php
	require_once \'../'.$this->configPath.$this->configPageName.'\';
	$conn = MySQL_connect($servername, $username, $password);
	MySQL_query(\'use \'.$dbname, $conn);
	MySQL_query(\'SET NAMES utf8\');
	require_once \'../'.$this->configPath.$this->commonPageName.'\';
	require_once \''.$this->libsPath.'shareFunction.php\';
	session_start();
	if($_SESSION[\'rights\']>2) {
		$url = base64_decode($_GET[\'url\']);
		header(\'Location: \'.$url);
	}
	$page = $_GET[\'page\'];';
	for ($i=0; $i < $fields; $i++) {
		if($_POST[$fieldNames[$i].'_type']==3) {
		$editCode .= '
	$'.$fieldNames[$i].' = $_POST[\''.$fieldNames[$i].'\'];
	$'.$fieldNames[$i].' = implode(\',\',$'.$fieldNames[$i].');';
		} elseif($_POST[$fieldNames[$i].'_type']==7) {
			$editCode .= '
	$'.$fieldNames[$i].' = getFilePath($_FILES[\''.$fieldNames[$i].'\']);
	if($'.$fieldNames[$i].'>=1)
		$'.$fieldNames[$i].' = $_POST[\''.$fieldNames[$i].'_value\'];';
		} elseif($_POST[$fieldNames[$i].'_type']!=9)
			$editCode .= '
	$'.$fieldNames[$i].' = $_POST[\''.$fieldNames[$i].'\'];';
	}
	$editCode .= '
	$update_time = date(\'Y-m-d H:i:s\');
	if($_GET[\'action\']==\'edit\') {
		$query = MySQL_query("UPDATE '.$tableName.' SET ';
	for ($i=0; $i < $fields; $i++) {
		if($i==0)
			$editCode .= $fieldNames[$i].' = '.'\'$'.$fieldNames[$i].'\'';
		else
			$editCode .= ', '.$fieldNames[$i].' = '.'\'$'.$fieldNames[$i].'\'';
	}
	$editCode .= ' WHERE '.$fieldNames[0].' = \'$'.$fieldNames[0].'\'");
		if($query==1 && isset($_GET[\'fkid\']))
			header(\'Location: '.$this->listPageName.'?page=\'.$page.\'&fkid=\'.$_GET[\'fkid\']);
		else
			header(\'Location: '.$this->listPageName.'?page=\'.$page);
	} else {
		$'.$fieldNames[0].' = $_GET[\''.$fieldNames[0].'\'];
		$rs = MySQL_query("SELECT * FROM '.$tableName.' WHERE '.$fieldNames[0].' = \'$'.$fieldNames[0].'\'");
		MySQL_num_rows($rs);
		$result=mysql_fetch_array($rs);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>edit '.$tableName.'</title>
		<link type="text/css" rel="stylesheet" href="'.$this->cssPath.'style.css">
		<script language="javascript" type="text/javascript" src="'.$this->libsPath.'datepicker/WdatePicker.js"></script>
	</head>
	<body class="theme3">
		<div>
			<fieldset id="addFormHeader">
				<input id="back" type="button" onclick="location.href=\''.$this->listPageName.'?page=<?php echo $page;if(isset($_GET[\'fkid\']))echo \'&fkid=\'.$_GET[\'fkid\']; ?>\'" value="返回列表" name="back">
				<h1>修改</h1>
			</fieldset>
			<form id="editForm" name="editForm" action="'.$this->editPageName.'?action=edit&page=<?php echo $page;if(isset($_GET[\'fkid\']))echo \'&fkid=\'.$_GET[\'fkid\']; ?>" method="post" enctype="multipart/form-data" >
				<fieldset id="addFormBody">';
		for ($i=0; $i < $fields; $i++) {
			if($_POST[$fieldNames[$i].'_type']==0 || $_POST[$fieldNames[$i].'_type']==10)	// 0或10 = 只读
				$editCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<span class="readonly">
							<span class="text"><?php echo $result[\''.$fieldNames[$i].'\']; ?></span>
						</span>
						<input type="hidden" id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'" value="<?php echo $result[\''.$fieldNames[$i].'\']; ?>">
					</div>';
			elseif($_POST[$fieldNames[$i].'_type']==1)	// 1 = text输入框
				$editCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<input type="text" id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'" value="<?php echo $result[\''.$fieldNames[$i].'\']; ?>">
					</div>';
			elseif($_POST[$fieldNames[$i].'_type']==2) {	// 2 = 下拉菜单
				$editCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<span class="readonly">
							<select id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'">';
				for ($j=0; $j < $_POST[$fieldNames[$i].'_options_count']; $j++) {
					if($j==0)
						$editCode .= '
								<option id="'.$fieldNames[$i].'_v'.$j.'" value="">'.$_POST[$fieldNames[$i].'_option_'.$j].'</option>';
					else
						$editCode .= '
								<option id="'.$fieldNames[$i].'_v'.$j.'" value="'.$_POST[$fieldNames[$i].'_option_'.$j].'">'.$_POST[$fieldNames[$i].'_option_'.$j].'</option>';
				}
				$editCode .= '
							</select>
						</span>
					</div>
					<script>
					for(var i=0;i<'.$_POST[$fieldNames[$i].'_options_count'].';i++){
						if("<?php echo $result[\''.$fieldNames[$i].'\']; ?>"==document.getElementById("'.$fieldNames[$i].'_v"+i).value){
							document.getElementById("'.$fieldNames[$i].'_v"+i).selected = true;
						}
					}
					</script>';
			} elseif($_POST[$fieldNames[$i].'_type']==3) {	// 3 = 多项选择
				$editCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<span class="readonly">';	
				for ($j=0; $j < $_POST[$fieldNames[$i].'_options_count']; $j++) {			
					$editCode .= '
							<input type="checkbox" id="'.$fieldNames[$i].'_v'.$j.'" name="'.$fieldNames[$i].'[]" value="'.$_POST[$fieldNames[$i].'_option_'.$j].'">'.$_POST[$fieldNames[$i].'_option_'.$j];
				}
				$editCode .= '
						</span>
					</div>
					<?php $options = explode(\',\',$result[\''.$fieldNames[$i].'\']); ?>
					<script>
					var option_counts = new Array();
					<?php 
					for($i=0;$i<count($options);$i++){
						echo \'option_counts[\'.$i.\']="\'.$options[$i].\'";\';
					}
					?>
					for(var i=0;i<option_counts.length;i++){
						for(var j=0;j<'.$_POST[$fieldNames[$i].'_options_count'].';j++){
							if(option_counts[i]==document.getElementById("'.$fieldNames[$i].'_v"+j).value){
								document.getElementById("'.$fieldNames[$i].'_v"+j).checked = true;
							}
						}
					}
					</script>';
			} elseif($_POST[$fieldNames[$i].'_type']==4) {	// 4 = 单项选择
				$editCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label><span class="readonly">';	
				for ($j=0; $j < $_POST[$fieldNames[$i].'_options_count']; $j++) {			
					$editCode .= '
								<input type="radio" id="'.$fieldNames[$i].'_v'.$j.'" name="'.$fieldNames[$i].'" value="'.$_POST[$fieldNames[$i].'_option_'.$j].'">'.$_POST[$fieldNames[$i].'_option_'.$j].'';
				}
				$editCode .= '
						</span>
					</div>
					<script>
					for(var i=0;i<'.$_POST[$fieldNames[$i].'_options_count'].';i++){
						if("<?php echo $result[\''.$fieldNames[$i].'\']; ?>"==document.getElementById("'.$fieldNames[$i].'_v"+i).value){
							document.getElementById("'.$fieldNames[$i].'_v"+i).checked = true;
						}
					}
					</script>';
			} elseif($_POST[$fieldNames[$i].'_type']==5) {	// 5 = 日期控件
				$editCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<input class="Wdate" type="text" id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'"onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" value="<?php echo $result[\''.$fieldNames[$i].'\']; ?>">
						<img onclick="WdatePicker({el:\''.$fieldNames[$i].'\', dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" src="'.$this->cssPath.'datePicker.png" width="32" height="32" align="absmiddle">
					</div>';
			} elseif($_POST[$fieldNames[$i].'_type']==6)	// 6 = textarea文本输入框
				$editCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<textarea cols="50" rows="10" id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'"><?php echo $result[\''.$fieldNames[$i].'\']; ?></textarea>
					</div>';
			elseif($_POST[$fieldNames[$i].'_type']==7)	// 7 = 上传图片
				$editCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<input type="file" id="'.$fieldNames[$i].'" name="'.$fieldNames[$i].'">
						<input type="hidden" id="'.$fieldNames[$i].'_value" name="'.$fieldNames[$i].'_value" value="<?php echo $result[\''.$fieldNames[$i].'\']; ?>">
						<?php
						if($result[\''.$fieldNames[$i].'\']!=\'\')
							echo \'<div class="photodiv"><a target="_blank" href="\'.$result[\''.$fieldNames[$i].'\'].\'"><img class="photoimg" src="\'.$result[\''.$fieldNames[$i].'\'].\'"></a></div>\';
						?>
					</div>';
			elseif($_POST[$fieldNames[$i].'_type']==8) {	// 8 = 外键
				$fkTable = $_POST[$fieldNames[$i].'_option_0'];
				$editCode .= '
					<div>
						<label class="edituser">'.$fieldNames[$i].'：</label>
						<span class="readonly">
							<a target="_blank" href="../'.$fkTable.'/'.$this->listPageName.'?fkid=<?php echo $result[0]; ?>">'.$fkTable.'</a>
						</span>
					</div>';
			}
		}
		$editCode .= '
				</fieldset>
				<fieldset id="addFormFooter">
					<input value="保存" type="submit" />
				</fieldset>
			</form>
		</div>
	</body>
</html>
<?php
	}
?>';
		$fh = fopen($this->filePath.$this->editPageName, 'w');
		$r = fwrite($fh, $editCode);
		fclose($fh);
		return $r;
	}

	/***************************************
	 *								       *
	 *			生成delete.php页面		   *
	 *								       *
	 ***************************************/
	public function genDeletePage($tableName, $fieldNames) {
		$fields = count($fieldNames);
		$deleteCode .= '<?php
require_once \'../'.$this->configPath.$this->configPageName.'\';
$conn = MySQL_connect($servername, $username, $password);
MySQL_query(\'use \'.$dbname, $conn);
MySQL_query(\'SET NAMES utf8\');
$'.$fieldNames[0].' = $_GET[\''.$fieldNames[0].'\'];
$update_time = date(\'Y-m-d H:i:s\');
if($_GET[\'action\']==\'del\') {
	$query = MySQL_query("UPDATE '.$tableName.' SET '.$fieldNames[$fields-1].' = 1 WHERE '.$fieldNames[0].' = ".$'.$fieldNames[0].');
	if($query==1)
		echo json_encode(1);
	else
		echo json_encode(0);
}
?>';
		$fh = fopen($this->filePath.$this->deletePageName, 'w');
		$r = fwrite($fh, $deleteCode);
		fclose($fh);
		return $r;
	}
	
	/*************************************
	 *								     *
	 *			生成list.js页面		     *
	 *								     *
	 *************************************/
	public function genListJs($fieldNames) {
		$deleteJsCode .= 'function deleteRecord(id) {
	var c = confirm("确定删除？");
	if(c){
		$(document).ready(function(){
			$.ajax({
				type:"GET",
				url:"'.$this->deletePageName.'?'.$fieldNames[0].'="+id+"&action=del",
				dataType:"json",
				cache:false,
				success:function(msg){
					if(1==msg) {
						alert("刪除成功");
						location.reload();
					} else{
						alert("刪除失败");
					}
				}
			});
		})
	}
}

function addRecord(page,url) {
	if(document.getElementById("hasFK"))
		location.href="'.$this->addPageName.'?page="+page+"&fkid="+document.getElementById("hasFK").value+"&url="+url;
	else
		location.href="'.$this->addPageName.'?page="+page+"&url="+url;
}

function editRecord(param) {
	if(document.getElementById("hasFK"))
		location.href="'.$this->editPageName.'?"+param+"&fkid="+document.getElementById("hasFK").value;
	else
		location.href="'.$this->editPageName.'?"+param;
}';
		$fh = fopen($this->filePath.$this->jsPath.'list.js', 'w');
		$r = fwrite($fh, $deleteJsCode);
		fclose($fh);
		return $r;
	}
	
	/*************************************
	 *								     *
	 *			生成login.php页面		 *
	 *								     *
	 *************************************/
	public function genLoginPage() {
		$loginCode .= '<?php
	require_once \'../../'.$this->configPath.$this->configPageName.'\';
	$conn = MySQL_connect($servername, $username, $password);
	MySQL_query(\'use \'.$dbname, $conn);
	MySQL_query(\'SET NAMES utf8\');
	if($_GET[\'action\']==\'login\') {
		$url = base64_decode($_POST[\'url\']);
		if($url==\'\')
			$url = \''.$this->listPageName.'\';
		$loginname = $_POST[\'loginname\'];
		$password = $_POST[\'password\'];
		$rs = MySQL_query("SELECT * FROM cmsg_user WHERE loginname = \'$loginname\'");
		MySQL_num_rows($rs);
		$result=mysql_fetch_array($rs);
		if(md5($password)==$result[\'password\']) {
			session_start();
			$_SESSION[\'lid\'] = $result[\'cuid\'];
			$_SESSION[\'rights\'] = $result[\'rights\'];
			header(\'Location: \'.$url);
		} else
			header(\'Location: '.$this->loginPageName.'\');
	} else {
		if($_GET[\'action\']==\'logout\') {
			session_start();
			unset($_SESSION[\'lid\']);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>login</title>
		<link rel="stylesheet" href="'.$this->cssPath.'reset.css">
		<link rel="stylesheet" href="'.$this->cssPath.'animate.css">
		<link rel="stylesheet" href="'.$this->cssPath.'styles.css">
	</head>
	<body>
		<div id="container">
			<form id="loginForm" name="loginForm" action="login.php?action=login" method="post" enctype="multipart/form-data">
				<label for="name">Username:</label>
				<input type="name" name="loginname">
				<label for="username">Password:</label>
				<!--<p><a href="#">Forgot your password?</a>-->
				<input type="password" name="password">
				<input type="hidden" name="url" value="<?php echo $_GET[\'url\']; ?>">
				<div id="lower">
					<input type="checkbox"><label class="check" for="checkbox">Keep me logged in</label>
					<input type="submit" value="Login">
				</div>
			</form>
		</div>
	</body>
</html>
<?php
	}
?>';
		$fh = fopen($this->filePath.$this->loginPageName, 'w');
		$r = fwrite($fh, $loginCode);
		fclose($fh);
		return $r;
	}
}
?>