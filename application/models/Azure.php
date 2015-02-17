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
            $this->cloudService = AZURE_CLOUDSERVICE;

            $this->load->library("azureRestClient",['cloudservice' => $this->cloudService,
                                                    'deploymentname' => "lti123xigq9",
                                                    'subscriptionid' => AZURE_SUBSCRIPTION_ID,
                                                    'certificate' => AZURE_CERTIFICATE]);

            //check if the cloudservice for the course exists if not creates it            
            if(!$this->checkCloudService($this->cloudService)){                
                if($this->addCloudService($this->cloudService))
                    redirect("error");                
            }
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
     * TODO : let it add more than 1 at the same time
     */
    function addVMRole($data){

        $sourceimage = $this->getSourceImageDetails($data['osimage_id']);
        if(!empty($sourceimage)){
            // if($data['numtocreate'] > 1){
                $rand = $this->randString(6);
                $add['rolename'] = $this->cloudService."-".$rand;
                $add['hostname'] = $this->cloudService."-".$rand;
                $add['username'] = 'admin';
                $add['password'] = 'HolaHola1_';
                $add['medialink'] = AZURE_MEDIALINK."vm-role-medialink-".$rand.".vhd";
                $add['sourceimagename'] = $sourceimage['name'];
                $add['os']    	        = strtolower($sourceimage['os']);  
                $add['externalport'] = rand(100,50000);

            // }      

            //ok before we add the new VM we need to check if we have a deployment on our cloudservice            
            $deployments = $this->azurerestclient->getCloudServiceDeployments("production");
            
            if($deployments['success']){                
                $result = $this->azurerestclient->addVMRole($add);
                if($result['success']){
                    $return = ['type' => 'success','msg' => "VM created"];
                }else
                    $return = ['type' => 'warning','msg' => isset($result['response']) ? $result['response'] : 'Error Creating VM Role with Deployment'];
                
            }else{
                //ok we have a cloudservice without a vm deployment , lets create our first vm together with a deployment
                $add['name'] = $this->cloudService;
                $add['label'] = $this->cloudService.$rand;                                
                $result = $this->azurerestclient->addVMRoleDeployment($add);                
                if($result['success']){
                    $return = ['type' => 'success','msg' => "VM created"];
                }else
                    $return = ['type' => 'warning','msg' => isset($result['response']) ? $result['response'] : 'Error Creating VM'];
                
            }
            return $return;            
        }        
        return array("type" => "error","msg" => "Couldn't find the sourceimage name in DB");    	
    }
        

    /**
     * Returns the os image name that we have on database 
     */
    function getSourceImageDetails($id){        
        $result = $this->db->get_where("os_images",array("id" => $id));
        return $result->row_array();           
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
     * Shutsdown a vm role
     */
    function shutdownVMRole($rolename){
        $result = $this->azurerestclient->shutdownVMRole($rolename);
        if($result['success']){
            $msg = ['type' => 'success','msg' => 'VM has been shutdown correctly'];
        }else
            $msg = ['type' => 'warning','msg' => isset($result['response']) ? $result['response'] : 'There was a problem stopping down the VM'  ];

        return $msg;
    }
    /**
     * Starts a vm role
     */
    function startVMRole($rolename){
        $result = $this->azurerestclient->startVMRole($rolename);        
        if($result['success']){
            $msg = ['type' => 'success','msg' => 'VM started correctly'];
        }else
            $msg = ['type' => 'warning','msg' => isset($result['response']) ? $result['response'] : 'There was a problem starting the VM' ];

        return $msg;
    }

    /**
     * makes a random string to specific lenght
     */
    function randString($length){
        $alphabet = "0123456789abcdefghijklmnopqrstuvwxyz";
          $s = "";
            for ($i = 0; $i != $length; ++$i){
                $s .= $alphabet[mt_rand(0, strlen($alphabet) - 1)];
            }
        return $s;
    }



}//end of class