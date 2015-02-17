<?php defined('BASEPATH') OR exit('No direct script access allowed');

$table = "No VM's available";

if(!empty($vms)){
	
	$table  = "<table class='table table-striped'>";
	$table .= "<th>".lang("vm_name")."</th><th>".lang("vm_status")."</th>
			  <th>".lang("vm_image")."</th>
			  <th>".lang("os")."</th><th>".lang("vm_assigned")."</th><th>Actions</th>";

	foreach($vms as $value){
		$icon ="";
		if($value['roleInfo']->PowerState == "Started") 
			$icon = '<span class="glyphicon glyphicon-ok green"></span>';
		if($value['roleInfo']->PowerState == "Stopped")
			$icon = '<span class="glyphicon glyphicon-stop red"></span>';


		$table.="<tr class='".$value['roleInfo']->RoleName."'><td>".$value['roleInfo']->RoleName."</td>				
				<td><div class='status'>".$icon."&nbsp;&nbsp;".$value['roleInfo']->PowerState."</div></td>
				<td>".$value['extraInfo']['body']->OSVirtualHardDisk->SourceImageName."</td>
				<td>".$value['extraInfo']['body']->OSVirtualHardDisk->OS."</td>";

		$table .= "<td>Not assigned</td>";					
		$table .= '<td><div class="dropdown">
					  <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    Actions
					    <span class="caret"></span>
					  </button>
					  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
					    <li role="presentation"><a href="#" class="stop">'.lang('stop').'</a></li>
					    <li role="presentation"><a href="#" class="start">'.lang('start').'</a></li>
					    <li role="presentation"><a href="#" class="assignstudent">'.lang('assign_to_student').'</a></li>					    
					  </ul>
					</div></td>';			
	}
}
echo "<h1>".lang("vms")."</h1>";
echo $table;
?>

<div id='msg' >
</div>

<script>

$(document).ready(function(){

	// Stops/shutsdown a VM role
	$(".stop").click(function(){
		var rolename = $(this).parents("tr").attr("class");
		var $tr = $("tr."+rolename);	
		setStatusMsg("stop",$tr);	
		$.ajax({
		  type: "POST",
		  url: "<?=site_url("/manage/stopvm/")?>",
		  data: { rolename: rolename },
		  dataType : 'json'
		}).done(function(e) {
		 	setMsg(e.type,e.msg);
		 	if(e.type === "success"){
		 		updateStatusMsg("stopped",$tr);
		 	}
		});
	})


	// Starts a VM role
	$(".start").click(function(){

		var rolename = $(this).parents("tr").attr("class");
		$tr = $("tr."+rolename);
		setStatusMsg("start",$tr);
		$.ajax({
		  type: "POST",
		  url: "<?=site_url("/manage/startvm/")?>",		  
		  data: { rolename: rolename },
		  dataType : 'json'
		}).done(function(e) {			
			setMsg(e.type,e.msg);
			if(e.type === "success"){
		 		updateStatusMsg("started",$tr);
		 	}else{
		 		updateStatusMsg("error",$tr);
		 	}
		});
	});

	//once the required action is finisshed from ajax, we set the new status for the vm
	updateStatusMsg = function(type,$status){

		if(type === "started"){
			var html = '<span class="glyphicon glyphicon-ok green"></span>&nbsp;&nbsp;<?=lang('vm_started')?>';
			$status.find("div.status").html(html);
		}
		if(type === "stopped"){
			var html = '<span class="glyphicon glyphicon-stop red"></span>&nbsp;&nbsp;<?=lang('vm_stopped')?>';
			$status.find("div.status").html(html);
		}
		if(type === "error"){
			var html = '<span class="glyphicon glyphicon-alert yellow"></span>&nbsp;&nbsp;<?=lang('refresh_page')?>';
			$status.find("div.status").html(html);
		}		
	}

	//changes the status msgs to the action required  stopping/starting
	setStatusMsg = function (type,$status){

		if(type === "start"){
			var html = " <img src='<?=base_url("assets/img/loadingcon.gif")?>'> <?=lang('vm_starting')?>..";
			$status.find("div.status").html(html);
		}
		if(type === "stop"){
			var html = " <img src='<?=base_url("assets/img/loadingcon.gif")?>'> <?=lang('vm_stopping')?>..";
			$status.find("div.status").html(html);
		}
	}

	// Sets a message 
	setMsg = function(type,msg){
	 	var r = '<div class="alert alert-'+type+'" role="alert">'+msg+'</div>';  
		$("#msg").html(r);
	}

});
</script>

