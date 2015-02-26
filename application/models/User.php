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
     * return user id or false
     */
    public function userExists($ltiUserId,$consumerInfoId){
        $query = $this->db->get_where("user_data",array("lti_user_id" => $ltiUserId,"consumer_info_id" => $consumerInfoId));    	
		if($query->num_rows() > 0){					
			$row = $query->row();
			return $row->id;
		}
		return false;
    }


    /**
     * adds the user to the user_data 
     * returns true/false
     */
    public function add($data){        
    	if(!$this->userExists($data['user_id'],$data['consumer_info_id'])){
    		$add = array();    		
    		$add['firstname'] = $data['firstname'];
    		$add['lastname'] = $data['lastname'];
    		$add['email'] = $data['email'];
    		$add['lang']  = $data['lang'];
    		$add['role']  = $data['is_teacher'] ? 'teacher' : 'student';
    		$add['consumer_info_id'] = $data['consumer_info_id'];    		
    		$add['lti_user_id'] = $data['user_id'];
            $add['created'] = date("Y-m-d H:i:s");
    		return $this->db->insert("user_data",$add);
     	}
     	return true;//it was already there
    }

    /**
     * return all the data from the user_data table
     * return object
     */
    public function getUser($id){
    	$query = $this->db->get_where("user_data",array("id" => $id));
		if($query->num_rows() > 0){
			return  $query->row();			
		}
		return false;
    }

    /**
     * Create a session for the user
     * return true/false
     */
    public function createSession($userID){

    	$data = $this->getUser($userID);
    	if($data){
    		$this->session->userid = $data->id;
    		$this->session->role = $data->role;
    		return true;
    	}    	
    	return false;
    }    

    /**
     *  Return a list of all students     
     */
    public function getStudents(){

        $query = $this->db->get_where("user_data",array("role" => "student"));
        if($query->num_rows() > 0){
            return  $query->result();          
        }
        return false;
    }

    


}