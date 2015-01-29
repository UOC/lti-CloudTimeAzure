<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error extends CI_Controller {

	public function __construct(){ 		
		parent::__construct();
	}

	public function index()
	{

		show_error("lkjasdlfkj");

		if($error == 'cannot_create_user'){

			show_error("Cannot create user");
		}
	}
}
