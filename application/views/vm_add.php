<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo "<h1>".lang("vm_add")."</h1>";
		
?>
<p><?=lang("vm_will_be_created_at")?><b><?=CLOUDSERVICE?></b></p>
<form>
  <div class="form-group">
    <label for="exampleInputEmail1"><?=lang('vm_name')?></label>
    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter <?=lang('vm_name')?>">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>
  <div class="form-group">
    <label for="exampleInputFile"><?=lang("select_vm_image")?></label>
    <select name='vm_image' class="form-control">
    	<option value='-1'>Select</option>
    </select>
    <!-- <p class="help-block">Example block-level help text here.</p> -->
  </div>
  <div class="checkbox">
    <label>
      <input type="checkbox"> Check me out
    </label>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>