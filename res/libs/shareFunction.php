<?php
/**
 * 後臺分頁函數
 * 
 * 處理并顯示分頁
 *
 * @param int $num 總條目數量
 * @param int $perpage 每頁顯示數目
 * @param int $curpage 當前頁數
 * @param string $mpurl 分頁連接，請注意要傳遞的參數不要遺漏
 * @param int $maxpages 最多顯示頁數
 * @return string 分頁
 */
function multiAdmin($num, $perpage, $curpage, $mpurl, $fkidparam, $maxpages = 0) {
	$mpurl .= strpos($mpurl, '?') ? '&' : '?';
	if($num > $perpage) {
		$page = 5;
		$offset = 3;

		$realpages = ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

		$from = $curpage - $offset;
		$to = $curpage + $page - $offset - 1;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $curpage - $pages + $to;
				$to = $pages;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$from = $pages - $page + 1;
				}
			}
		}

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<li><a href="'.$mpurl.'page=1&'.$fkidparam.'">首頁</li>' : '').
		($curpage > 1 ? '<li><a href="'.$mpurl.'page='.($curpage - 1).'&'.$fkidparam.'">上一頁</a></li>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<li class="pager-current">'.$i.'</li>' :
			'<li><a href="'.$mpurl.'page='.$i.'&'.$fkidparam.'">'.$i.'</a></li>';
		}

		$multipage .= ($curpage < $pages ? '<li><a href="'.$mpurl.'page='.($curpage + 1).'&'.$fkidparam.'">下一頁</a></li>' : '').
		($curpage + $page - $offset <= $pages ? '<li><a href="'.$mpurl.'page='.$pages.'&'.$fkidparam.'">尾頁</a></li>' : '').
		($curpage == $maxpages ? '&nbsp;<a href="misc.php?action=maxpages&pages='.$maxpages.'">&gt;<b>?</b></a>&nbsp;' : '').
		($pages > $page ? '&nbsp;&nbsp;跳到第&nbsp;<input type="text" name="custompage" size="2" style="border: 1px solid; width:20px; padding: 4px; " onKeyDown="javascript: if(event.keyCode == 13) window.location=\''.$mpurl.'page=\'+this.value;">&nbsp;頁' : '');

		$multipage = $multipage ? ''.
		'<ul class="pager">'.'&nbsp;共'.$num.'條記錄&nbsp;&nbsp;'.$curpage.'/'.$realpages.$multipage.'</ul>'.
		'' : '';
	}
	return $multipage;
}

function htmlencode($str,$br=1){
	if(empty($str)) return;
	if($str=="") return $str;
	$str=htmlspecialchars($str);
	if($br==1){
		$str=str_replace(chr(13).chr(10),"<br />",$str);
	}
	return $str;
} //end htmlencode

/**
 * 過濾特殊字符函數
 * 
 * 過濾用於SQL注入的特殊字符，分整型和字符串兩種過濾方法
 *
 * @param $t_Val 源字符串
 * @param $isNum 是否數字
 * @return string 過濾後的字符串
 */
function checkParam($t_Val, $isNum = true) {
	// INT
	if($isNum) {
		if (is_numeric($t_Val))
			return $t_Val;
		else
			return 0;
	}
		
	// String
	$t_Val = str_replace("&", "&amp;",$t_Val); 
	$t_Val = str_replace("<", "&lt;",$t_Val);
	$t_Val = str_replace(">", "&gt;",$t_Val);
	if(get_magic_quotes_gpc()) {
		$t_Val = str_replace("\\\"", "&quot;",$t_Val);
		$t_Val = str_replace("\\''", "&#039;",$t_Val);
	} else {
		$t_Val = str_replace("\"", "&quot;",$t_Val);
		$t_Val = str_replace("'", "&#039;",$t_Val);
	}
	return $t_Val;
}

/**
 * 获取上传图片的存放路径
 *
 * @param $file 上传的文件
 * @return string 图片的存放路径
 */
function getFilePath($file) {
	$allowedExts = array('jpg', 'jpeg', 'gif', 'png');
	$uploadPath = 'upload/';
	$maxFileSize = 4000000;
	$extension = end(explode('.', $file['name']));
	$fileNameFormat = date('YmdHis').rand(0, 9).'.'.$extension;
	if ((($file['type'] == 'image/gif')
		|| ($file['type'] == 'image/jpeg')
		|| ($file['type'] == 'image/png')
		|| ($file['type'] == 'image/pjpeg'))
		&& ($file['size'] < $maxFileSize)
		&& in_array($extension, $allowedExts)) {
		if ($file['error'] > 0) {
			return 1;
		} else {
			if (file_exists($uploadPath.date('YmdHis').rand(0, 9).'.'.$extension)) {
				return 2;
			} else {
				$flag = move_uploaded_file($file['tmp_name'], $uploadPath.$fileNameFormat);
				if($flag){
					chmod($uploadPath.$fileNameFormat, 0777);
				}
				return  $uploadPath.$fileNameFormat;
			}
		}
	} else {
		return 3;
	}
}
?>