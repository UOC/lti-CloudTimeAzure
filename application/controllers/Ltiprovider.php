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

		if($tool_provider->user->isAdmin() || $tool_provider->user->isStaff())
			$userdata['is_teacher'] = true;
		else
			$userdata['is_teacher'] = false;
		
		$userId = $this->User->userExists($userdata['consumer'],$userdata['context_id'],$userdata['user_id']);
		if(!$userId){
			$userId = $this->User->add($userdata);						 
		}

		//is all ok lets create the session for the user
		if($userId){			
		 	if($this->User->createSession($userId))
		 		redirect('/test');
		 	else
		 		show_error('Could not create session',200,'Error');
		}

	show_error('Could not create user',200,'Error');
}
	
}