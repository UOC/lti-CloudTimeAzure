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
        $this->load->library("azureRestClient",['cloudservice' => $this->session->cloudservicename,
                                                'deployment' => $this->session->cloudservicename,
                                                'subscriptionid' => AZURE_SUBSCRIPTION_ID,
                                                'certificate' => AZURE_CERTIFICATE]);        
        //$this->initCloudService($this->session->cloudservicename);   
        $this->cloudService = $this->session->cloudservicename;     
    }

    /**
     * Initialize the cloud service 
     */
    function initCloudService($name){        
        //check if the cloudservice for the course exists if not creates it            
        if(!$this->checkCloudService($name)){                
            if($this->addCloudService($name))
                die("Could not create cloud service ".$name); //TODO fix this
        }
        $this->cloudService = $name;
    }
    /**
     * Checks if a cloudservice exists
     */
    function checkCloudService($name){
        $result = $this->azurerestclient->checkCloudService($name);                

        if($result['success']) 
            return true;
        else
            return false;
    }

    /**
     * Creates a cloudservice
     */
    function addCloudService($name,$location = "West Europe"){

        $add['base64label'] = base64_encode(microtime());
        $add['name'] = $name;        
        $add['location'] = $location;
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
                $externalport = $this->getExternalPort();
                $password = $this->makePassword(13);
                $rand = $this->randString(6);
                $add['rolename'] = $this->cloudService."-".$rand;
                $add['hostname'] = $this->cloudService."-".$rand;
                $add['username'] = 'lti';
                $add['password'] = $password;
                $add['medialink'] = AZURE_MEDIALINK."vm-role-medialink-".$rand.".vhd";
                $add['sourceimagename'] = $sourceimage['name'];
                $add['os']    	        = strtolower($sourceimage['os']);  
                $add['externalport'] = $externalport;                   
            // }      
            //ok before we add the new VM we need to check if we have a deployment on our cloudservice            
            $deployments = $this->azurerestclient->getCloudServiceDeployments("production");            
            if($deployments['success']){                
                $result = $this->azurerestclient->addVMRole($add);
                if($result['success']){
                    //we have created our virtual machine, lets save the necessary details.
                    $this->saveVmDetails($add);
                    $return = ['type' => 'success','msg' => "VM created"];
                }else
                    $return = ['type' => 'warning','msg' => isset($result['response']) ? $result['response'] : 'Error Creating VM Role with Deployment'];
                
            }else{
                //ok we have a cloudservice without a vm deployment , lets create our first vm together with a deployment
                $add['name'] = $this->cloudService;
                $add['label'] = $this->cloudService.$rand;                                
                $result = $this->azurerestclient->addVMRoleDeployment($add);                
                if($result['success']){
                    $this->saveVmDetails($add);
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
     * Returns a list of all assigned virtual machines for a student
     */     
    function getStudentVms($userid){
        $result = $this->db->get_where("user_vms",array("user_id" => $userid));
        return $result->result_array();
    }

    /**
     * Assigns a student to a VM
     * Returns an array with the result information
     */
    function assignStudent($data){
            
        $insert = array();
        $insert['rolename']  =  $data['rolename'];        
        $insert['user_id']   =  $data['studentid'];
        $insert['azure_vm_id'] = $data['azure_vm_id'];

        if($this->db->insert("user_vms",$insert))
            $return = array("type" => "success","msg" => "The student has been assigned to the virtual machine ".$rolename);
        else
            $return = array("type" => "danger","msg" => "An error ocurred when assigned this virtual machine to this student.");            
       

        return $return;
    }


    /**
     * Saves our new virtual machine details.
     */
    function saveVmDetails($add){
        $insert = array();
        $insert['rolename'] = $add['rolename'];
        $insert['username'] = $add['username'];
        $insert['password'] = $add['password'];
        $insert['externalport'] = $add['externalport'];
        $insert['os'] = $add['os'];
        $insert['cloudservice'] = $this->cloudService;
        $this->db->insert("azure_vm",$insert);
    }
    /**
     * Get all the vm details from virtual machines we have created.
     * it returns this in the format of  array("rolename" => array( details ))
     */
    function getCreatedVmDetails(){

        $result = $this->db->get_where("azure_vm",array("cloudservice" => $this->cloudService));
        $tmp = array();
        if($result->num_rows() > 0){            
            foreach($result->result_array() as $v){
                $tmp[$v['rolename']] = $v;
            }
        }

        return $tmp;
    }

    /**
     * gets us an external port that hastn been used for a new virtual machine
     */
    function getExternalPort(){
        $result = $this->db->query("select max(externalport) as externalport from azure_vm");
        $row = $result->row_array();
        $cport = (int)$row['externalport'];         
        return $cport + 1;
    }

    /**
     * creates a password to create a windows azure virtual machine.
     * it must contains from 6 to 72 characters, numbers , uppercaseletters and a special character.
     */
    function makePassword($length){
        $alphabet = "123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQRSTUVWXYZ";
          $s = "";
            for ($i = 0; $i != $length; ++$i){
                $s .= $alphabet[mt_rand(0, strlen($alphabet) - 1)];
            }
        return $s."$";
    }
    /**
     * Makes a random string to specific lenght
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