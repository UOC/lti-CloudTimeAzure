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
    function getOSImages(){
    	return $this->azurerestclient->getOSImages();
    }


    /**
     * Adds a new VM to windows azure
     */
    function addVM(){
    	$add['vhd'] = "community-4-3379fadd-83e5-4dc7-8a20-2088af38d95b-1.vhd";    	
    	$this->azurerestclient->addVM($add);    	
    }

    /**
     * Add a new VM Role
     * https://msdn.microsoft.com/en-us/library/azure/jj157186.aspx#OSVirtualHardDisk
     */
    function addVMRole(){
        $this->azurerestclient->addVMRole();
    }

    /**
     * Return a list of deployments for a specific cloud service
     */
    function getDeployments(){
        return $this->azurerestclient->getDeployments();
    }



    


}//end of class