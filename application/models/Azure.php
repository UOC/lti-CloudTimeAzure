<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Azure extends CI_Model {

    var $cloudService;

    public function __construct()
    {
            // Call the CI_Model constructor
            parent::__construct();
            $coursename = "lti123";//TMP

            $this->load->library("azureRestClient",array('cloudservice' =>"lti123") );
            //check if the cloudservice for the course exists if not creates it            
            if(!$this->checkCloudService($coursename)){                
                if($this->addCloudService($coursename))
                    redirect("error");                
            }
            
            $this->cloudService = $coursename;                    
    }

    /**
     * Checks if a cloudservice exists
     */
    function checkCloudService($name){
        $result = $this->azurerestclient->checkCloudService($name);        
        if($result['success']) return true;
        return false;
    }

    /**
     * Creates a cloudservice
     */
    function addCloudService($name){

        $add['base64label'] = base64_encode(microtime());
        $add['name'] = $name;        
        $add['location'] ='West Europe';
        $result = $this->azurerestclient->addCloudService($add);        
        if($result['success']){
            return true;
        }
        return false;
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
            // if($data['numtocreate'] > 1){
                $rand = $this->randString(5);
                $add['rolename'] = $this->cloudService."-".$rand;
                $add['hostname'] = $this->cloudService."-".$rand;
                $add['username'] = 'admin';
                $add['password'] = 'HolaHola1_';
                $add['medialink'] = AZURE_MEDIALINK."vm-role-medialink-".$rand.".vhd";
                $add['sourceimagename'] = $sourceimagename;
                $add['os']    	        = $data['os'];  
                $add['externalport'] = rand(100,50000);
            // }      

            //ok before we add the new VM we need to check if we have a deployment on our cloudservice            
            $deployments = $this->azurerestclient->getCloudServiceDeployments("production");
            if($deployments['success']){
                //TODO - Check that there is only 1 and is the one it should have
                return array("type" => "info" ,"msg" => $this->azurerestclient->addVMRole($add) );                
            }else{
                //ok we have a cloudservice without a vm deployment , lets create our first vm together with a deployment
                $add['name'] = $this->cloudService;
                $add['label'] = $this->cloudService.$rand;                
                return array("type" => "info" ,"msg" => $this->azurerestclient->addVMRoleDeployment($add) );                
            }            
        }        

        return array("type" => "error","msg" => "Couldn't find the sourceimage name in DB");    	
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
    function getVMRoles(){
        return $this->azurerestclient->getCloudServiceDeploymentRolesDetails();
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