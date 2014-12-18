<?php
	error_reporting(E_ALL ^ E_NOTICE);
	date_default_timezone_set(PRC);
	$servername = "127.0.0.1";
	$username = "pasco";
	$password = "123456";
	$c_dbName = "cmsg";
	$c_typeTableName = 'cmsg_field_type';
	$c_optionTableName = 'cmsg_field_option';
	$c_fkTableName = 'cmsg_table_fk';
	$c_userTableName = 'cmsg_user';
	$c_configPath = 'config/';
	$c_configPageName = 'config.php';
	$c_commonPageName = 'common.php';
	$c_cmsPath = 'cms/';
	$c_libsPath = '../libs/';
	$c_cssPath = '../css/';
	$c_jsPath = '../js/';
	$c_indexPageName = 'index.php';
	$c_listPageName = 'list.php';
	$c_addPageName = 'add.php';
	$c_editPageName = 'edit.php';
	$c_deletePageName = 'delete.php';
	$c_loginPageName = 'login.php';
?>