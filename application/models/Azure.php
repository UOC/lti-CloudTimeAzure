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
     * Returns a list of all available virtual machine images from the API.
     */
    function getOSImagesAPI(){
    	return $this->azurerestclient->getOSImages();
    }

    /**
     * Return all the OSimages from the DB
     */
    function getOSImages(){
        $this->db->select("label,id");
        $this->db->order_by('label asc');
        $result = $this->db->get("os_images");
        
        return $result->result_array();        
                
    }

    /**
     * Returns all the info from a specific OS Image
     */
    function getOSImageDetails($id){
        $result = $this->db->get_where("os_images",array("id" => $id));
        return $result->row_array();

    }
    
    /**
     * Add a new VM Role
     * https://msdn.microsoft.com/en-us/library/azure/jj157186.aspx#OSVirtualHardDisk
     */
    function addVMRole($data){

        $sourceimagename = $this->getSourceImageName($data['osimage_id']);
        if(!empty($sourceimagename)){
            $rand = $this->randString(5);
            $add['rolename'] = 'vm-azure-rolename-'.$rand;
            $add['hostname'] = 'vm-azure-hostname-'.$rand;
            $add['username'] = 'test123';
            $add['password'] = 'HolaHola1_';
            $add['medialink'] = AZURE_MEDIALINK."vm-role-medialink-".$rand.".vhd";
            $add['sourceimagename'] = $sourceimagename;
            $add['os']    	        = $data['os'];        
            return array("type" => "info" ,"msg" => $this->azurerestclient->addVMRole($add) );        
        }
        return array("type" => "error","msg" => "Could'nt find the source image name in DB");
    	
    }
        
    /**
     * Returns the os image name that we have on database 
     */
    function getSourceImageName($id){
        $this->db->select("name");
        $result = $this->db->get_where("os_images",array("id" => $id));
        $row = $result->row(); 
        return $row->name;   

    }

    /**
     * Return a list of deployments for a specific cloud service
     */
    function getDeployments(){
        return $this->azurerestclient->getDeployments();
    }


    /**
     * Imports the OSImages to os_images table 
     */
    function importOSImages(){

        $data = $this->getOSImagesAPI();         
        $insert = [];
        if(!empty($data)){                
            $a=0;
            foreach($data->OSImage as $img){
                $insert[$a]['category'] = (string)$img->Category;
                $insert[$a]['label'] = (string)$img->Label;
                $insert[$a]['name'] = (string)$img->Name;
                $insert[$a]['os'] = (string)$img->OS;
                $insert[$a]['description'] = (string)$img->Description;
            $a++;
            }            
        }
        return $this->db->insert_batch("os_images",$insert,true);
    }

    /**
     * makes a random string to specific lenght
     */
    function randString($length){
        $alphabet = "0123456789abcdefghijklmnopqrstuvwxyz";
          $s = "";
          for ($i = 0; $i != $length; ++$i)
            $s .= $alphabet[mt_rand(0, strlen($alphabet) - 1)];
          return $s;
    }



}//end of class