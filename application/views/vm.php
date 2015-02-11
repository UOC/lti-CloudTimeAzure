<?php defined('BASEPATH') OR exit('No direct script access allowed');

$table = "No VM's available";

if(!empty($vms)){
	
	$table  = "<table class='table table-striped'>";
	$table .= "<th>".lang("vm_name")."</th><th>".lang("vm_status")."</th><th>".lang("vm_image")."</th>";
	foreach($vms as $value){
		$icon ="";
		if($value['roleInfo']->PowerState == "Started") 
			$icon = '<span class="glyphicon glyphicon-ok green"></span>';
		if($value['roleInfo']->PowerState == "Stopped")
			$icon = '<span class="glyphicon glyphicon-stop red"></span>';

		$table.="<tr><td>".$value['roleInfo']->RoleName."</td>				
				<td>".$icon."&nbsp;&nbsp;".$value['roleInfo']->PowerState."</td>
				<td>".$value['extraInfo']['body']->OSVirtualHardDisk->SourceImageName."</td>";
	}	
}
echo "<h1>".lang("vms")."</h1>";
echo $table;

?>

