<?php
/**
 * Manages windows azure 
 * UOC 2015 - Victor Castillo ( victor@tresipunt.com )
 */

class Manage extends CI_Controller{
	
	public function __construct(){
        // Call the CI_Model constructor
        parent::__construct();

        //temp for tests //REMOVE
        // $this->session->cloudservicename = "lti123";
        // $this->session->role = "teacher";
        // $this->session->userid = 17;

        $this->lang->load('general', 'english');   
        if($this->session->role != "teacher"){
        	redirect("student");
        }
    }

	public function index(){		
		$this->template->load("main","manage",array());				
	}	

	/**
	 *  Lists all the virtual machines
	 */
	function vm(){
		$result = $this->Azure->getVMRoles();		
		$body ='';
		if($result['success']){
			$body = $result['body'];
		}
		$students = $this->User->getStudents();
		$vm_details = $this->Azure->getCreatedVmDetails();//get the details that we have in our db about the created vm
		$this->template->load("main","vm",array("vms" => $body,
												"students_list" => $students,
												"vm_details" => $vm_details));		
		
	}

	/**
	 * List all the available virtual machine images 
	 */
	function os_images($id = 0){
		$result = $this->Azure->getOSImages();		
		$OSImageDetails = "";
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
			// if($response['type'] == "success"){
			// 	redirect("manage/vm");
			// }
		}
		$osimages = $this->Azure->getOSImages();
		$this->template->load("main","vm_add",array("osimages" => $osimages,"msg" => $response) );			
	}


	/**
	 * Stops/shutsdown a virtual machine
	 * This should be called from ajax so we return a json
	 */
	function stopvm(){
		$result = array();
		if($this->input->post("rolename")){
			$rolename  = $this->input->post("rolename");
			$result = $this->Azure->shutdownVMRole($rolename);					
		}
		echo json_encode($result);
	}

	/**
	 * Starts a virtual machine
	 * This should be called from ajax so we return a json
	 */
	function startvm(){
		$result = array();
		if($this->input->post("rolename")){
			$rolename  = $this->input->post("rolename");
			$result = $this->Azure->startVMRole($rolename);			
		}
		echo json_encode($result);
	}

	/**
	 * Assignts a VM to a student
	 */
	function assignstudent(){
		
		$insert = array();
		$insert['rolename']  = $this->input->post("rolename");
		$insert['user_id']   = $this->input->post("studentid");		
		$azureRoleDetails = $this->Azure->getAzureRoleDetails($insert['rolename']);
		$insert['azure_vm_id'] = $azureRoleDetails['id'];
		
		$user = $this->User->getUser($insert['user_id']);
		// $vminfo = $this->Azure->;
        if($user){            
        	$return = $this->Azure->assignStudent($insert);
		}else
            $return = array("type" => "danger","msg" => "The students was not found.");			
		
		echo json_encode($return);
	}
	/**
	 * syncs all the OSimages from the api into our DB
	 */
	function import_osmages(){
		echo $this->Azure->importOSImages();
	}
	

}//end of class