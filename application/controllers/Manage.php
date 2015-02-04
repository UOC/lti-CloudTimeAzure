<?php
/**
 * Manages windows azure 
 * UOC 2015 - Victor Castillo ( victor@tresipunt.com )
 */

class Manage extends CI_Controller{
	
	public function __construct(){
        // Call the CI_Model constructor
        parent::__construct();
        $this->lang->load('general', 'english');                
    }

	public function index(){
		// $this->load->library("Azure");
		//echo $this->azure->serviceManagement();
		// $this->azure->createCloudService();
		$this->template->load("main","manage",array("hej"));		
		// $this->azure->roleInstancesStatus();
		//$this->azurerestclient->listLocations();
		// $this->azurerestclient->getCLoudServices();
		// $this->azurerestclient->getVM();
	}	

	/**
	 *  Lists all the virtual machines
	 */
	function vm(){
		$cloudServices = $this->Azure->getCLoudServices();		
		$this->template->load("main","vms",array("vms" => $cloudServices));		
	}

	/**
	 * adds a new virtual machine
	 */
	function vm_add(){
		$this->template->load("main","vm_add",array());			
	}
	
	


}//end of class