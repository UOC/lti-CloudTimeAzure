<?php
/***
* 	Manage the LTI sessions 
*	UOC 2015 - Victor Castillo ( victor@tresipunt.com )
*/


include(APPPATH."libraries/lti_tool_provider/LTI_Tool_Provider.php");


class Ltiprovider extends CI_Controller{

	public function __construct(){ 		
		parent::__construct();
		
	}

	public function index(){		
		
		//need to create a new connection object for the tool provider
		$dbLti = new mysqli($this->db->hostname, $this->db->username, $this->db->password, $this->db->database);
		$db_connector = LTI_Data_Connector::getDataConnector('',$dbLti);
		$tool = new LTI_Tool_Provider("doLaunch", $db_connector,$this);
		$tool->execute();
		mysqli_close($dbLti);
		
	}

	public function doLaunch($tool_provider) {
		
	    $userdata = array();
		// $userdata['username']   = $_REQUEST['custom_username'];//$tool_provider->resource_link->settings->custom_username;
		$userdata['firstname']  = $tool_provider->user->firstname;
		$userdata['lastname']   = $tool_provider->user->lastname;
		$userdata['fullname']   = $tool_provider->user->fullname;
		$userdata['email']      = $tool_provider->user->email;
		$userdata['user_id']    = $tool_provider->user->getId();
		$userdata['context_id'] = $_REQUEST['context_id'];
		// $userdata['launch_presentation_locale'] = $_REQUEST['launch_presentation_locale'];
		$userdata['consumer_key'] = $tool_provider->consumer->getKey();
		$userdata['lang'] ='english';
		
		//this will be the name of our cloudservice that will be used in windows azure aswel as the name of our deployment
		//This field can contain only letters, numbers, and hyphens. The first and last character in the field must be a letter or number. 
		$cleanConsumerKey = preg_replace("/[^a-zA-Z0-9]+/", "", $userdata['consumer_key']);
		$nameForCloudService = "lti".$userdata['context_id']."-".$cleanConsumerKey;

		//we will save the consumer_key and context_id on a table
		$consumer_info = $this->Lti->getConsumer($userdata['consumer_key'],$userdata['context_id']);
		if( !$consumer_info ){
			//if is not added , then we can create it.
			$consumer_info_id = $this->Lti->addConsumer($userdata['consumer_key'],$userdata['context_id']);
			// TODO - send to error if not created			
		}else
		$consumer_info_id = $consumer_info['id'];

		if(!$consumer_info) die("Error: Couldn't add consumer info."); //we need this to continue

		$userdata['consumer_info_id'] = $consumer_info_id;

		/** Session variables we need for the application to work **/
		$this->session->cloudservicename = $nameForCloudService;
		//out deploymentname will be the same as the cloudservice name for now
		$this->session->deployment = $nameForCloudService;
		$this->session->lang = $userdata['lang'];	

		if($tool_provider->user->isAdmin() || $tool_provider->user->isStaff()){	
			$userdata['is_teacher'] = true;			
			//lets check if there is a cloudservice for this course
			if(!$this->Azure->checkCloudService($nameForCloudService)){
				if( !$this->Azure->addCloudService($nameForCloudService)){
					die("Error: Couldn't add a new Cloud Service for this consumer.");
				}
			}
		}else{
			$userdata['is_teacher'] = false;
		}		
		

		$userId = $this->User->userExists($userdata['user_id'],$consumer_info_id);

		if(!$userId){
			$userId = $this->User->add($userdata);
		}		

		//is all ok lets create the session for the user
		if($userId){			
		 	if($this->User->createSession($userId)){		 		
		 		if($userdata['is_teacher'])
		 			redirect('/manage/vm');
		 		else
		 			redirect('/student');
		 	}
		 	else{
		 		die('Could not create session');
		 	}
		}

		die('Could not add user');
}
	
}