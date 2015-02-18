<?php defined('BASEPATH') OR exit('No direct script access allowed');

?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>        
      </button>
      <a class="navbar-brand" href="#"><img src="<?=base_url('/assets/img/marca_UOC_blanc_paper_small.png')?>"></a>
    </div>    
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">                
        <li><a href="<?=site_url('/manage/vm_add')?>">Add Virtual Machines</a></li>            
            <li><a href="<?=site_url('/manage/vm')?>">List VM</a></li>            
            <li><a href="<?=site_url('/manage/os_images')?>">List OS Images</a></li>         
      </ul>         
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
