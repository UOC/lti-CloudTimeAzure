<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Azure extends CI_Model {

    public function __construct()
    {
            // Call the CI_Model constructor
            parent::__construct();
            $this->load->library("azureRestClient");
    }

    /**
     * Returns all cloud services
     */
    function getCloudServices(){
    	return  $this->azurerestclient->getCLoudServices();
    }


}//end of class