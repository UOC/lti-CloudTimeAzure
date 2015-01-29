<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class User extends CI_Model {

    public function __construct()
    {
            // Call the CI_Model constructor
            parent::__construct();

    }
    
    /**
     * Checks if the user exists or not, meaning that if they have come for the first time or not
     * return true/false
     */
    public function userExists($userKey){
    	$query = $this->db->get_where("user_data",array("userkey" => $userKey));
		if($query->num_rows() > 0){					
			return true;
		}
		return false;
    }

    /**
     * adds the user to the user_data 
     * returns true/false
     */
    public function add($userKey,$data){

    	if(!$this->userExists($userKey)){
    		$add = array();
    		$add['userkey'] = $userKey;
    		$add['firstname'] = $data['firstname'];
    		$add['lastname'] = $data['lastname'];
    		$add['email'] = $data['email'];
    		$add['lang']  = $data['lang'];
    		$add['role']  = $this->getRole( $data['is_teacher'] ? 'teacher' : 'student');
    		return $this->db->insert("user_data",$add);
     	}
     	return true;//it was already there
    }

    /**
     * return all the data from the user_data table
     * return object
     */
    public function getData($userKey){

    	$query = $this->db->get_where("user_data",array("userkey" => $userKey));
		if($query->num_rows() > 0){
			return  $query->row();			
		}
		return false;
    }


    //returns the ID of the role name
    public function getRole($role){    		

		$query = $this->db->get_where("user_roles",array("name" => $role));
		if($query->num_rows() > 0){
			$row = $query->row();
			return $row->id;
		}

		return false;
    }

    /**
     * Create a session for the user
     * return true/false
     */
    public function createSession($userKey){

    	$data = $this->getData($userKey);
    	if($data){
    		$this->session->userkey = $data->userkey;
    		$this->session->role = $data->role;
    		return true;
    	}    	
    	return false;
    }

    /*
    Transform the user info from the lti consumer into a hashkey to use as the userkey 
    for this we use the consumer_key + context_id + user_id , cause this will be unique for each user on each
    LTi Consumer
    @return the userkey hash
     */
    public function userKey($consumerKey,$contextID,$UserId){
    	if(!empty($consumerKey) && !empty($contextID) && !empty($UserId)){
    		return hash('adler32',$consumerKey.$contextID.$UserId);
    	}
    	return false;
    }

}