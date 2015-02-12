<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!empty($osimages)){
  $options= "";
  foreach($osimages as $key => $value){
    $selected = "";
    if(!empty($selectedosimage) && $selectedosimage == $value['id'] ){ $selected = 'selected ="selected"';}
    $options .="<option value='".$value['id']."' ".$selected.">".$value['label']."</option>";
  }
}
echo "<h1>".lang("vm_add")."</h1>";		
if(!empty($msg)){
  echo '<div class="alert alert-'.$msg['type'].'" role="alert">'.(string)$msg['msg'].'</div>';  
}
?>
<p>Once you add a new Virtual Machine, it can take a few minutes until it will appear on the list of Virtual Machines.</p>
<form action='<?=site_url('manage/vm_add')?>' method='POST'>  
  <div class="form-group">
    <label for="exampleInputFile"><?=lang("select_vm_image")?></label>
    <select name='osimage_id' class="form-control">
      <?=$options;?>
    </select>
    <p class="help-block">Select an virtual machine os image to use for this new VM .</p>
  </div>  
  <?php
  /*
  <div class="form-group">
    <label for="exampleInputFile"><?=lang("select_copies")?></label>
    <select name='numtocreate' class="form-control">
    	<?php
      for($a =1;$a<=AZURE_MAXVMTOCREATE;$a++){
        echo "<option value='".$a."'>".$a."</option>";
      }?>
    </select>    
  </div>*/?>
  <button type="submit" class="btn btn-primary">Create</button>
</form>