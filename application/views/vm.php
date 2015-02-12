<?php defined('BASEPATH') OR exit('No direct script access allowed');

$table = "No VM's available";

if(!empty($vms)){
	
	$table  = "<table class='table table-striped'>";
	$table .= "<th>".lang("vm_name")."</th><th>".lang("vm_status")."</th><th>".lang("vm_image")."</th><th>Actions</th>";
	foreach($vms as $value){
		$icon ="";
		if($value['roleInfo']->PowerState == "Started") 
			$icon = '<span class="glyphicon glyphicon-ok green"></span>';
		if($value['roleInfo']->PowerState == "Stopped")
			$icon = '<span class="glyphicon glyphicon-stop red"></span>';
		$table.="<tr><td>".$value['roleInfo']->RoleName."</td>				
				<td>".$icon."&nbsp;&nbsp;".$value['roleInfo']->PowerState."</td>
				<td>".$value['extraInfo']['body']->OSVirtualHardDisk->SourceImageName."</td>";

		$table .= '<td><div class="dropdown">
					  <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    Actions
					    <span class="caret"></span>
					  </button>
					  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
					    <li role="presentation"><a href="#" id="stop" data-role="'.$value['roleInfo']->RoleName.'">"'.lang('stop').'"</a></li>
					    <li role="presentation"><a href="#" id="start">'.lang('start').'</a></li>
					    <li role="presentation"><a href="#" id="assignstudent">'.lang('assign_to_student').'</a></li>					    
					  </ul>
					</div></td>';			
	}
}
echo "<h1>".lang("vms")."</h1>";
echo $table;
?>
<script>
$(document).ready(function(){



});
</script>

