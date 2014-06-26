function deleteRecord(id) {
	var c = confirm("确定删除？");
	if(c){
		$(document).ready(function(){
			$.ajax({
				type:"GET",
				url:"delete.php?id="+id+"&action=del",
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
		location.href="add.php?page="+page+"&fkid="+document.getElementById("hasFK").value+"&url="+url;
	else
		location.href="add.php?page="+page+"&url="+url;
}

function editRecord(param) {
	if(document.getElementById("hasFK"))
		location.href="edit.php?"+param+"&fkid="+document.getElementById("hasFK").value;
	else
		location.href="edit.php?"+param;
}