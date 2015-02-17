<?php defined('BASEPATH') OR exit('No direct script access allowed');

$options = "No OS images available.";
if(!empty($osimages)){
	$options= "";
	foreach($osimages as $key => $value){
		$selected = "";
		if(!empty($selectedosimage) && $selectedosimage == $value['id'] ){ $selected = 'selected ="selected"';}
		$options .="<option value='".$value['id']."' ".$selected.">".$value['label']."</option>";
	}
}
echo "<h1>".lang("osimages")."</h1>";
?>
<script>
$(document).ready(function(){
	$('#osimages').change(function(){		
		var id = $(this).val();		
		window.location.href="<?=site_url('manage/os_images/"+id+"')?>";
	})
})
</script>
<div class="row">
	<div class="col-md-12">
	<form>
	  <div class="form-group">  
	  <label for="osimages"><?=lang("osimages_selectone")?></label>  
	    <select name='osimages' id='osimages' class="form-control">
	    <?=$options?>
	    </select>
	  </div> 
	</form>
	</div>
	<div class="col-md-12" >
		<?php if(!empty($osimagedetails)){
			
			echo "<p><b>Category:</b> ".$osimagedetails['category']."</p>";
			echo "<p><b>Label:</b> ".$osimagedetails['label']."</p>";
			echo "<p><b>OS:</b> ".$osimagedetails['os']."</p>";
			echo "<p><b>Description:</b> ".$osimagedetails['description']."</p>";
		} ?>

	</div>
</div>


