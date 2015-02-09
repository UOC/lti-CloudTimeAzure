<?php defined('BASEPATH') OR exit('No direct script access allowed');

$table = "No VM's available";

if(!empty($deployments)){
	$table  = "<table class='table table-striped'>";
	$table .= "<th>".lang("vm_name")."</th><th>".lang("vm_ip")."</th><th>".lang("vm_status")."</th><th>".lang("vm_image")."</th>";
	foreach($deployments as $value){

		$icon ="";
		if($value['deploymentInfo']->PowerState == "Started") 
			$icon = '<span class="glyphicon glyphicon-ok green"></span>';
		if($value['deploymentInfo']->PowerState == "Stopped")
			$icon = '<span class="glyphicon glyphicon-stop red"></span>';

		$table.="<tr><td>".$value['deploymentInfo']->RoleName."</td>
				<td>".$value['deploymentInfo']->IpAddress."</td>
				<td>".$icon."&nbsp;&nbsp;".$value['deploymentInfo']->PowerState."</td>
				<td>".$value['roleInfo']->OSVirtualHardDisk->SourceImageName."</td>";
	}
	
}
echo "<h1>".lang("vms")."</h1>";
echo $table;

?>

