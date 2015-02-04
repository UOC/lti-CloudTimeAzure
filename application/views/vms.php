<?php
$table = "No VM's available";
if(!empty($vms)){
	$table  = "<table class='table table-striped'>";
	$table .= "<th>".lang("vm_name")."</th><th>".lang("vm_location")."</th><th>".lang("vm_status")."</th>";
	foreach($vms as $value){
		$table.="<tr><td>".$value['name']."</td><td>".$value['location']."</td><td>".$value['status']."</td>";
	}
	
}
echo "<h1>".lang("vms")."</h1>";
echo $table;

?>

