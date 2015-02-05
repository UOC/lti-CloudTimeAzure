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


    /**
     * Returns a list of all available virtual machine images.
     */
    function getVMImages(){
    	return $this->azurerestclient->getVmImages();
    }


    /**
     * Adds a new VM to windows azure
     */
    function addVM(){

    	$add['vhd'] = "community-4-3379fadd-83e5-4dc7-8a20-2088af38d95b-1.vhd";
    	
    	$this->azurerestclient->addVM($add);
    	


    }


}//end of class