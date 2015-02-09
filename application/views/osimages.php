<?php defined('BASEPATH') OR exit('No direct script access allowed');


$options = "No OS images available.";
if(!empty($osimages)){
	

	$tmp = [];
	$a = 0;
	foreach($osimages->OSImage as $img){				
		$tmp[$a] = (string)$img->Label;
		$a++;
	}
	asort($tmp);//we needed this in order to order the array	
	foreach($tmp as $key => $value){
		$options .="<option value='".$key.">".$value."</option>";
	}
	
	// $table  = "<table class='table table-striped'>";
	// $table .= "<th>".lang("osimage_label")."</th>
	// 		   <th>".lang("osimage_os")."</th>
	// 		   <th>".lang("osimage_desc")."</th>
	// 		   <th>".lang("vm_status")."</th><th>".lang("vm_image")."</th>";		
}
echo "<h1>".lang("osimages")."</h1>";
// echo $table;
echo "<pre>";
print_r($osimages);
echo "</pre>";
?>
<form>
  <div class="form-group">  
  <label for="osimages"><?=lang("osimages_selectone")?></label>  
    <select name='osimages' class="form-control">
    <?=$options?>
    </select> so should I put all this object on a json in javascript and when they select one I get this info from the json that ill be in the html
  </div>
 
</form>
