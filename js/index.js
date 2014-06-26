window.onload=function() {
	// get tab container
  	var container = document.getElementById("tabContainer");
	var tabcon = document.getElementById("tabscontent");
    // set current tab
    var navitem = document.getElementById("tabHeader_"+currentTab);
    //store which tab we are on
    var ident = navitem.id.split("_")[1];
    navitem.parentNode.setAttribute("data-current",ident);
    //set current tab with class of activetabheader
    navitem.setAttribute("class","tabActiveHeader");
    //hide two tab contents we don't need
	if(currentTab==0)
		document.getElementById("tabpage_1").style.display="none";
	else
		document.getElementById("tabpage_0").style.display="none";

}

function allCheck(addOrEdit) {
	var field = "";
	if(addOrEdit=="edit")
		field = "edit_all_check";
	else
		field = "add_all_check";
	var isChecked = document.getElementById(field).checked;
	var checkboxs = document.getElementsByName("show_fields[]");
	for(var i=0;i < checkboxs.length; i++){
		checkboxs[i].checked = isChecked;
	}
}

function changeCheck(addOrEdit) {
	var field = "";
	if(addOrEdit=="edit")
		field = "edit_all_check";
	else
		field = "add_all_check";
	var isChecked = true;
	var checkboxs = document.getElementsByName("show_fields[]");
	var begin = 0;
	var end = checkboxs.length/2;
	if(addOrEdit=="edit") {
		begin = checkboxs.length/2;
		end = checkboxs.length;
	}
	for(var i=begin;i < end; i++){
		if(!checkboxs[i].checked) {
			isChecked = false;
		}
	}
	document.getElementById(field).checked = isChecked;
}

function changePage(x, y) {
	document.getElementById("tabHeader_" + y).removeAttribute("class");
	document.getElementById("tabHeader_" + x).setAttribute("class","tabActiveHeader");
	document.getElementById("tabpage_" + y).style.display="none";
	document.getElementById("tabpage_" + x).style.display="block";
}
function changeType(fieldName, addOrEdit) {
	var field = '';
	if(addOrEdit=='edit')
		field = 'edit_'+fieldName;
	else
		field = fieldName;
	document.getElementById(field+'_options').innerHTML = '';
	var obj = document.getElementById(field+'_type');
	var index = obj.selectedIndex;			// 索引
	//var text = obj.options[index].text;	// 文本
	var value = obj.options[index].value;	// 值
	if(value>1&&value<5){
		document.getElementById(field+'_options').innerHTML = '<input value="add" type="button" onclick="addOption(\''+fieldName+'\',\''+addOrEdit+'\')" /><input value="del" type="button" onclick="delOption(\''+fieldName+'\',\''+addOrEdit+'\')" /><input type="hidden" id="'+field+'_options_count" name="'+fieldName+'_options_count" value=1><div><input type="text" id="'+field+'_option_0" name="'+fieldName+'_option_0" value=""></div>';
	}else if(value==8){
		document.getElementById(field+'_options').innerHTML = '<div><input type="hidden" id="'+field+'_options_count" name="'+fieldName+'_options_count" value=1><input type="text" id="'+field+'_option_0" name="'+fieldName+'_option_0" value=""></div>';
	}
}

function addOption(fieldName, addOrEdit) {
	var field = '';
	if(addOrEdit=='edit')
		field = 'edit_'+fieldName;
	else
		field = fieldName;
	var num = parseInt(document.getElementById(field+'_options_count').value);
	var thenew= document.createElement('div'); 
	thenew.innerHTML = '<input type="text" id="'+fieldName+'_option_'+num+'" name="'+fieldName+'_option_'+num+'" value="">';
	document.getElementById(field+'_options').appendChild(thenew);
	document.getElementById(field+'_options_count').value = num+1;
}

function delOption(fieldName, addOrEdit) {
	var field = '';
	if(addOrEdit=='edit')
		field = 'edit_'+fieldName;
	else
		field = fieldName;
	var num = parseInt(document.getElementById(field+'_options_count').value);
	if(num>1) {
		document.getElementById(field+'_options').removeChild(document.getElementById(field+'_options').childNodes[2+num]);
		document.getElementById(field+'_options_count').value = num-1;
	}
}

function changeDB(field) {
	var dbname = document.getElementById(field+"dbname").value;
	var tablename = document.getElementById(field+"tablename");
	tablename.innerHTML = '<option value="">请选择table</option>';
	$(document).ready(function(){
		$.ajax({
			type:"POST",
			url:"listtable.php",
			dataType:"json",
			data:"dbname="+dbname,
			cache:false,
			success:function(msg){
				for(var i=0;i<msg.length;i++){
					tablename.innerHTML += "<option value="+msg[i]+">"+msg[i]+"</option>";
				}
			}
		});
	})
}