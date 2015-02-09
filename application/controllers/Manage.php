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
	function deployments(){
		$result = $this->Azure->getDeployments();		
		$this->template->load("main","deployments",array("deployments" => $result));		
	}

	/**
	 * Lista all the available virtual machine images 
	 */

	function os_images(){

		$result = $this->Azure->getOSImages();
		// echo "<pre>";
		// print_r($result);
		// echo "</pre>";

		$this->template->load("main","osimages",array("osimages" => $result));			

	}

	/**
	 * adds a new virtual machine
	 */
	function vm_add(){
		// $this->Azure->getVMImages();
		// $this->Azure->addVM();
		   // $this->Azure->addVMRole();
		// $d = $this->Azure->getDeployments();
		// echo "<pre>";
		// print_r($d);
		// echo "</pre>";

		$this->template->load("main","vm_add",array());			

	}

	


}//end of class