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
    	if(!$this->userExists($data['consumer'],$data['context_id'],$data['user_id'])){
    		$add = array();    		
    		$add['firstname'] = $data['firstname'];
    		$add['lastname'] = $data['lastname'];
    		$add['email'] = $data['email'];
    		$add['lang']  = $data['lang'];
    		$add['role']  = $data['is_teacher'] ? 'teacher' : 'student';
    		$add['consumer'] = $data['consumer'];
    		$add['context_id'] = $data['context_id'];
    		$add['lti_user_id'] = $data['lti_user_id'];
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

    /**
     * Assigns a student to a VM
     * Returns an array with the result information
     */
    function assignStudent($rolename,$studentid){

        $return = array();
        $user = $this->getUser($studentid);
        if($user){
            if(empty($user->assigned_vm)){
                $update  = array("assigned_vm" => $rolename );
                if($this->db->update("user_data",$update,"id = ".$studentid))
                    $return = array("type" => "success","msg" => "The student has been assigned to the virtual machine ".$rolename);
            }else{
                $return = array("type" => "danger","msg" => "This student already has an assigned VM");
            }
        }else
            $return = array("type" => "danger","msg" => "The students was not found.");

        return $return;
    }


}