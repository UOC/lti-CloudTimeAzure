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
	function os_images($id){

		$result = $this->Azure->getOSImages();		

		if(!empty($id)){
			$OSImageDetails = $this->Azure->getOSImageDetails($id);
		}
		$this->template->load("main","osimages",array("osimages" => $result,
													  "osimagedetails" => $OSImageDetails,
													  "selectedosimage" => $id));			

	}

	/**
	 * adds a new virtual machine/s
	 */
	function vm_add(){		
		//form sent to add a new vm 
		$response = "";
		if($this->input->post()){
			$postData = $this->input->post();			
			$response = $this->Azure->addVMRole($postData);
			if($response['type'] == "success"){
				redirect("manage/deployments");
			}
		}
		$osimages = $this->Azure->getOSImages();
		$this->template->load("main","vm_add",array("osimages" => $osimages,"response" => $response) );			
	}

	/**
	 * syncs all the OSimages from the api into our DB
	 */
	function import_osmages(){
		echo $this->Azure->importOSImages();
	}
	


}//end of class