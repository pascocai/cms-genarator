<?php
	function request_uri() {
		if (isset($_SERVER['REQUEST_URI'])) {
			$uri = $_SERVER['REQUEST_URI']; 
		} else {
			if (isset($_SERVER['argv'])) {
				$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
			} else {
				$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
			}
		}
		return base64_encode($uri);
	}
	session_start();
	if(isset($_SESSION['lid'])) {
		//echo 'rights:'.$_SESSION['rights'];
	} else {
		$uri = request_uri();
		header('Location: login.php?url='.$uri);
	}
?>