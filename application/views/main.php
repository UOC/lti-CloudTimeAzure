<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Main template file
 */
?>
<!DOCTYPE html>
<html>
<head>
<title>UOC - Windows Azure</title>
<link rel='stylesheet' type='text/css' href="<?=base_url('/assets/bootstrap_3.3.2/css/bootstrap_cosmo_theme.css')?>" />
<link rel='stylesheet' type='text/css' href="<?=base_url('/assets/css/styles.css')?>" />
<script src="<?=base_url('/assets/js/jquery-1.11.2.min.js')?>"></script>
<script src="<?=base_url('/assets/bootstrap_3.3.2/js/bootstrap.min.js')?>"></script>
</head>
<body>
<div class='container'>
	<div class='row' id='header'>
		<div class='col-md-10'>
		 <?php $this->load->view("header");?>
		</div>
		<div class='col-md-2'>
			<img  class='azureLogo' src='<?=base_url('/assets/img/windows_azure_logo.png')?>'>	
		</div>
	</div>	
	<div class='row' id='content'>
		<div class='col-md-12'>
			<?=$contents?>
		</div>
	</div>
</div>
</body>
</html>
