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
    public function userExists($consumer,$contextId,$userId){
    	$query = $this->db->get_where("user_data",array("consumer" => $consumer,"context_id" => $contextId,"lti_user_id" => $userId));
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
    	if(!$this->userExists($data['consumer'],$data['context_id'],$data['user_id'])){
    		$add = array();    		
    		$add['firstname'] = $data['firstname'];
    		$add['lastname'] = $data['lastname'];
    		$add['email'] = $data['email'];
    		$add['lang']  = $data['lang'];
    		$add['role']  = $this->getRole( $data['is_teacher'] ? 'teacher' : 'student');
    		$add['consumer'] = $data['consumer'];
    		$add['context_id'] = $data['context_id'];
    		$add['lti_user_id'] = $data['user_id'];
    		return $this->db->insert("user_data",$add);
     	}
     	return true;//it was already there
    }

    /**
     * return all the data from the user_data table
     * return object
     */
    public function getUserData($id){

    	$query = $this->db->get_where("user_data",array("id" => $id));
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
    public function createSession($userID){

    	$data = $this->getUserData($userID);
    	if($data){
    		$this->session->userid = $data->id;
    		$this->session->role = $data->role;
    		return true;
    	}    	
    	return false;
    }    

}