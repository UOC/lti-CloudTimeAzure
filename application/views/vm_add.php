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

if(!empty($response)){
  echo '<div class="alert alert-'.$response['type'].'" role="alert">'.$response['msg'].'</div>';  
}

?>
<p><?=lang("vm_will_be_created_at")?><b><?=CLOUDSERVICE?></b></p>
<form action='<?=site_url('manage/vm_add')?>' method='POST'>  
  <div class="form-group">
    <label for="exampleInputFile"><?=lang("select_vm_image")?></label>
    <select name='osimage_id' class="form-control">
      <?=$options;?>
    </select>
    <p class="help-block">Select an virtual machine os image to use for this new VM .</p>
  </div>
  <div class="form-group">
    <label for="exampleInputFile"><?=lang("select_os")?></label>
    <select name='os' class="form-control">
      <option value='windows'>Windows</option>
      <option value='linux'>Linux</option>
    </select>
    <p class="help-block">It has to match the Virtual Machine image OS.</p> 
  </div>
  <div class="form-group">
    <label for="exampleInputFile"><?=lang("select_copies")?></label>
    <select name='numtocreate' class="form-control">
    	<?php
      for($a =1;$a<=AZURE_MAXVMTOCREATE;$a++){
        echo "<option value='".$a."'>".$a."</option>";
      }?>
    </select>
    <p class="help-block">It has to match the Virtual Machine image OS.</p> 
  </div>
  
  <button type="submit" class="btn btn-primary">Create</button>
</form>