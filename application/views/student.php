<?php defined('BASEPATH') OR exit('No direct script access allowed');

?>
<div class='row'>
	<div class='col-md-12'>
		<h2><?=lang("your_assigned_vm")?></h2>	
	</div>
	<div class='col-md-12'>
	<?php  if(!empty($student_vms)){ ?>	
		<table class='table table-striped'>
		<thead>
			<tr>
				<th>
					<?=lang('os')?>
				</th>
				<th>
					<?=lang('vm_connection_info')?>
				</th>
				<th>
					<?=lang('vm_name')?>
				</th>
			</tr>
		</thead>
		<?php
			foreach($student_vms as $vm){
				echo "<tr><td>".$vm['os']."</td><td>".$vm['os']."</td></tr>";
			}
		?>
		</table>	
	<?php }else echo lang("no_vm_assigned");
	?>
	</div>
</div>