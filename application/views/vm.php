<?php defined('BASEPATH') OR exit('No direct script access allowed');

//lets get all the users that have a role from the $students_list array
if(!empty($students_list)){
	foreach($students_list as $key => $val){
		$options_for_students_list = "<option value='".$val->id."'>".$val->firstname. " ".$val->lastname."</option>";
	}
}

$table = lang("no_vm_available");

if(!empty($vms)){
	$table  = "<table class='table table-striped'>";
	$table .= "<th>".lang("vm_name")."</th><th>".lang("vm_status")."</th>
			  <th>".lang("vm_image")."</th>
			  <th>".lang("os")."</th><th>".lang("vm_assigned")."</th><th>Actions</th>";

	foreach($vms as $value){
		$rolename = (string)$value['roleInfo']->RoleName;
		$icon ="";
		if($value['roleInfo']->PowerState == "Started") 
			$icon = '<span class="glyphicon glyphicon-ok green"></span>';
		if($value['roleInfo']->PowerState == "Stopped")
			$icon = '<span class="glyphicon glyphicon-stop red"></span>';

		$table.="<tr class='".$rolename."'><td>".$rolename."</td>				
				<td><div class='status'>".$icon."&nbsp;&nbsp;".$value['roleInfo']->PowerState."</div></td>
				<td>".$value['extraInfo']['body']->OSVirtualHardDisk->SourceImageName."</td>
				<td>".$value['extraInfo']['body']->OSVirtualHardDisk->OS."</td>";

		$table .= "<td class='td_student'>Not assigned</td>";					
		$table .= '<td><div class="dropdown">
					  <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    Actions
					    <span class="caret"></span>
					  </button>
					  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
					    <li role="presentation"><a href="#" class="stop">'.lang('stop').'</a></li>
					    <li role="presentation"><a href="#" class="start">'.lang('start').'</a></li>
					    <li role="presentation"><a href="#" class="assignstudent">'.lang('assign_to_student').'</a></li>					    
					    <li role="presentation">
					    <div class="connectioninfo_'.$rolename.'" style="display:none">
					     ';
						if(isset($vm_details[$rolename])){
							$role_details = $vm_details[$rolename];
							$table .= "<p>ssh -p  ".$role_details['externalport']." ".$role_details['username']."@".$role_details['cloudservice'].".cloudapp.net</p>";
							$table .= "<p>".lang("password").": ".$role_details['password']." </p>";
						}else 
							$table .= lang("no_details");			

		$table .= 	    '</div>
					    <a href="#" class="connectioninfo">'.lang('connection_info').'</a></li>					    
					  </ul>
					</div></td>';			
	}
}
echo "<h1>".lang("vms")."</h1>";
echo $table;
?>
<div id='msg' >
</div>
<!-- Modal to assing a user to a virtual machine. -->
<div class="modal fade" id="student_assign_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=lang("assign_to_student");?></h4>
      </div>
      <div class="modal-body">
        <form>
        <input type='hidden' name='sa_rolename'  id='sa_rolename' value=''>
	        <!-- Lists of all the students without a vm	 -->
	        <select id='students_list' name='student_list' class="form-control">
	        <?php
	        if(!empty($students_list)){
	        	foreach($students_list as $key => $val){
	        		echo "<option value='".$val->id."'>".$val->firstname. " ".$val->lastname."</option>";
	        	}
	        }
	        ?>	
	        </select>				
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>   
		<button type="button" class="btn btn-primary" id='assign_to_student'><?=lang("assign_to_student")?></button>     
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal to view the connection info to the virtual machine. -->
<div class="modal fade" id="connection_info">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=lang("connection_info");?></h4>
      </div>
      <div class="modal-body">        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>		
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
$(document).ready(function(){

	/*triggers a modal and shows the connection info*/
	$(".connectioninfo").click(function(){
		var $this = $(this);
		var $tr = $this.parents("tr");
		var rolename = $tr.attr("class");
		var html = $tr.find(".connectioninfo_"+rolename).html();		
		$("#connection_info .modal-body").html(html);
		$("#connection_info").modal('show');
	})

	/** Triggers a modal to assig a user to a VM **/
	$(".assignstudent").click(function(){
		var $this = $(this);
		var $tr = $this.parents("tr");
		var rolename = $tr.attr("class");		
		$("#student_assign_modal").modal('show');	
		$("#sa_rolename").val(rolename);
	})	


	$("#assign_to_student").click(function(){
			var $this = $(this);
			var student_id = $("#students_list").val();
			var student_name = $("#students_list").text();
			var rolename = $("#sa_rolename").val();
			$("#student_assign_modal").modal('hide');
			$.ajax({
			  type: "POST",
			  url: "<?=site_url("/manage/assignstudent")?>",
			  data: { 'rolename': rolename,'studentid' : student_id },
			  dataType : 'json'
			}).done(function(e) {			 	
				setMsg(e.type,e.msg);
			 	if(e.type === "success"){
			 		$tr.find(".td_student").html(student_name);
			 	}
			});
	});



	// Stops/shutsdown a VM role
	$(".stop").click(function(){
		var rolename = $(this).parents("tr").attr("class");
		var $tr = $("tr."+rolename);	
		setStatusMsg("stop",$tr);	
		$.ajax({
		  type: "POST",
		  url: "<?=site_url("/manage/stopvm")?>",
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

