<?php
/**
 * Manages all
 * UOC 2015 - Victor Castillo ( victor@tresipunt.com )
 */


class Manage extends CI_Controller{
	

	public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

	public function index(){		 	
		// $this->load->library("Azure");
		//echo $this->azure->serviceManagement();
		// $this->azure->createCloudService();
		$this->template->load("main","manage",array("hej"));

	}	
	

}