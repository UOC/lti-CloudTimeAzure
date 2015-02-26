<?php
/**
 * Manages windows azure 
 * UOC 2015 - Victor Castillo ( victor@tresipunt.com )
 */

class Student extends CI_Controller{
	var $student_id; 
	
	public function __construct(){
        // Call the CI_Model constructor
        parent::__construct();
        $this->lang->load('general', 'english');  
        if($this->session->role == "student"){ 
        	$this->student_id = $this->session->userid;
    	}else
    		redirect("manage");

    }

	public function index(){
		$student_vms = $this->Azure->getStudentVms($this->student_id);
		$this->template->load("main","student",array("student_vms" => $student_vms));				
	}	


}