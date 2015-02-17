<?php defined('BASEPATH') OR exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Stream\StreamInterfaceuse;
use GuzzleHttp\Exception\RequestException;

class azureRestClient{

    var $client;
    var $subscriptionId;
    var $azureCertificate;
    var $cloudServiceName;
    var $deploymentName;
    var $certificate;

    function __construct($params){             
         $this->cloudServiceName = $params['cloudservice'];
         $this->deploymentName   = $params['deploymentname'];
         $this->subscriptionId   = $params['subscriptionid'];
         $this->certificate   = $params['certificate'];        
         $this->doConnect();
    }

    /**
     * Connects to the service 
     */
    function doConnect(){
        $this->client = new GuzzleHttp\Client(
            array('base_url' => 'https://management.core.windows.net/'.$this->subscriptionId."/" ,
                  'defaults' => array(
                        'headers' => array('x-ms-version' => '2014-06-01','Content-Type' => 'application/xml'),
                        'cert' => $this->certificate
                   )
            )
            );
        
    }

    /**
     * List all the locations available
     */
    function listLocations(){

        $r = ['success' => true];
        try{
            $response =  $this->client->get('locations');
            if($response->getStatusCode() == "202"){
                $r['success'] = true;            
                $r['body'] = $response->xml();
             }                        
        }catch (RequestException $e) {                
            // echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                $r['response'] = (string)$e->getResponse()->getBody();   
            }                
        }     
     return $r;    
    }

    /**
     * Returns all windows azure CloudServices for the subscription
     */
    // function getCLoudServices(){

    //     $response = $this->client->get("services/hostedservices");
    //     $result = $response->xml();
    //     $return = array();        
    //     if(!empty($result)){
    //         $a = 0;
    //         foreach($result->HostedService as $k => $v){                
    //             $return[$a]['name'] = (string)$v->ServiceName;
    //             $return[$a]['location'] = (string)$v->HostedServiceProperties->Location;
    //             $return[$a]['status'] = (string)$v->HostedServiceProperties->Status;
    //             $a++;
    //         }
    //     }
    //     return $return;
    // }


    /**
     * returns an array with all available OS Images
     */
    function getOSImages(){
        $r = ['success' => false];
        try {
            $response = $this->client->get("services/images");
            if($response->getStatusCode() == "200"){
                $r['success'] = true;            
                $r['body'] = $response->xml();
            }
        }catch (RequestException $e) {                
                // echo $e->getRequest() . "\n";
                if ($e->hasResponse()) {
                    $r['response'] = (string)$e->getResponse()->getBody();   
                }                
            }     
         return $r;
    }

    /**
     * Adds a new VM Role deployment to windows azure under a cloudservice 
     * https://msdn.microsoft.com/en-us/library/azure/jj157194.aspx
     */
    function addVMRoleDeployment($data){
                
        $body ='<Deployment xmlns="http://schemas.microsoft.com/windowsazure" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                  <Name>'.$data['name'].'</Name>
                  <DeploymentSlot>Production</DeploymentSlot>
                  <Label>'.$data['label'].'</Label>
                  <RoleList>
                    <Role>
                      <RoleName>'.$data['rolename'].'</RoleName>
                      <RoleType>PersistentVMRole</RoleType>
                      <ConfigurationSets>';

        if($data['os'] == "linux"){

            $body .= '<ConfigurationSet i:type="LinuxProvisioningConfigurationSet">
                        <ConfigurationSetType>LinuxProvisioningConfiguration</ConfigurationSetType>
                        <HostName>'.$data['hostname'].'</HostName>
                        <UserName>'.$data['username'].'</UserName>
                        <UserPassword>'.$data['password'].'</UserPassword>                                                   
                    </ConfigurationSet>';

            $body .= '<ConfigurationSet>
                          <ConfigurationSetType>NetworkConfiguration</ConfigurationSetType>          
                          <InputEndpoints>
                            <InputEndpoint>                              
                              <LocalPort>22</LocalPort>
                              <Name>SSH</Name>
                              <Port>'.$data['externalport'].'</Port>                              
                              <Protocol>TCP</Protocol>                                                                                                                    
                            </InputEndpoint>
                          </InputEndpoints>
                          </ConfigurationSet>'; 
        }
        $body .='</ConfigurationSets><OSVirtualHardDisk>
                        <MediaLink>'.$data['medialink'].'</MediaLink>
                        <SourceImageName>'.$data['sourceimagename'].'</SourceImageName>
                        <OS>'.$data['os'].'</OS>
                </OSVirtualHardDisk> ';

        $body .= '</Role>
                  </RoleList>
                </Deployment>';         

        $r = ["success" => false];    
        try{
            $response = $this->client->post('services/hostedservices/'.$this->cloudServiceName.'/deployments', array("body" => $body));            
            if($response->getStatusCode() == "202"){
                $r['success'] = true;            
            }

            $r = "status_code = ".$response->getStatusCode();

        }catch (RequestException $e) {
            // $r['request'] = $e->getRequest();

            if($e->hasResponse()) {                
                $r['response'] = (string)$e->getResponse()->getBody();   
            }
        }        
        return $r;
    }

    /**
     * Adds a new VM role into an already created deployment on a cloudservice 
     * https://msdn.microsoft.com/en-us/library/azure/jj157186.aspx
     */
    function addVMRole($data){
        
        $body ='<PersistentVMRole xmlns="http://schemas.microsoft.com/windowsazure" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                  <RoleName>'.$data['rolename'].'</RoleName>
                  <RoleType>PersistentVMRole</RoleType>                  
                      <ConfigurationSets>';

        //if the os is linux then we pass the specific xml config for it
        if($data['os'] == 'linux'){
            $body .= '<ConfigurationSet i:type="LinuxProvisioningConfigurationSet">
                        <ConfigurationSetType>LinuxProvisioningConfiguration</ConfigurationSetType>
                        <HostName>'.$data['hostname'].'</HostName>
                        <UserName>'.$data['username'].'</UserName>
                        <UserPassword>'.$data['password'].'</UserPassword>                                                   
                    </ConfigurationSet>';

            $body .= '<ConfigurationSet>
                          <ConfigurationSetType>NetworkConfiguration</ConfigurationSetType>          
                          <InputEndpoints>
                            <InputEndpoint>                              
                              <LocalPort>22</LocalPort>
                              <Name>SSH</Name>
                              <Port>'.$data['externalport'].'</Port>                              
                              <Protocol>TCP</Protocol>                                                                                                                    
                            </InputEndpoint>
                          </InputEndpoints></ConfigurationSet>';        
        }

        $body .='</ConfigurationSets><OSVirtualHardDisk>
                        <MediaLink>'.$data['medialink'].'</MediaLink>
                        <SourceImageName>'.$data['sourceimagename'].'</SourceImageName>
                        <OS>'.$data['os'].'</OS>
                </OSVirtualHardDisk>                  
                </PersistentVMRole>';   
        error_log($body);
        $r = ["success" => false];   
        try {
           $response =  $this->client->post('services/hostedservices/'.$this->cloudServiceName.'/deployments/'.$this->deploymentName.'/roles', array("body" => $body));                        
           if($response->getStatusCode() == "201" || $response->getStatusCode() == "202"){ 
                $r['success'] = true;            
            }
            $r['status_code'] = "status_code = ".$response->getStatusCode();

        }catch (RequestException $e) {                
            //$r['request'] = $e->getRequest();
            if ($e->hasResponse()) {
                $r['response'] = (string)$e->getResponse()->getBody();                
            }
        }
        return $r;
    }

    /**
     * Return all the VM deployments role info for a cloud service slot ( production or staging ) 
     */
    function getCloudServiceDeploymentRolesDetails($slot = "production"){

        $r = ['success' => false];
        try {
            $response = $this->client->get("services/hostedservices/".$this->cloudServiceName."/deploymentslots/".$slot);
            
            if($response->getStatusCode() == "200"){ 
                $r['success'] = true;
                $r['body'] = [];
                $xml = $response->xml();                    
                if($xml){
                    foreach($xml as $key => $value){
                        $a = 0;
                        foreach($value->RoleInstance as $ri){
                            $r['body'][$a]['roleInfo'] = $ri;
                            $r['body'][$a]['extraInfo'] = $this->getRole($ri->RoleName);
                            $a++;
                        }
                    }            
                }
            }
            $r['status_code'] = "status_code = ".$response->getStatusCode();
            
            }catch (RequestException $e) {                    
                $r['request'] =  $e->getRequest();                          
                if ($e->hasResponse()) {                
                    $r['response'] = (string)$e->getResponse()->getBody();                
                }                    
        }

        return $r;
    }

    /**
     * Return all the VM deployments role info for a cloud service slot ( production or staging ) 
     */
    function getCloudServiceDeployments($slot = "production"){

        $r = ['success' => false];
        try {
            $response = $this->client->get("services/hostedservices/".$this->cloudServiceName."/deploymentslots/".$slot);

            if($response->getStatusCode() == "200"){ 
                $r['success'] = true;
            }
            $r['status_code'] = "status_code = ".$response->getStatusCode();
            
            }catch (RequestException $e) {                    
                 $r['request'] =  $e->getRequest();        
                 echo "<pre>";
                 print_r($r); 
                 echo "</pre>";        
                if ($e->hasResponse()) {                    
                    $r['response'] = (string)$e->getResponse()->getBody();     
                }                    
        }

        return $r;
    }

    /**
     * Return info about a vm role 
     * https://msdn.microsoft.com/en-us/library/azure/jj157193.aspx
     */
    function getRole($rolename = ''){
        
        $r = ['success' => false];
        try{
            $response = $this->client->get("services/hostedservices/".$this->cloudServiceName."/deployments/".$this->deploymentName."/roles/".$rolename);
            $r['body'] = $response->xml();        
        }catch (RequestException $e) {
            // $r['request'] = $e->getRequest();
            if ($e->hasResponse()) {
                $r['response'] = (string)$e->getResponse()->getBody();     
            }            
        }
        return $r;
    }

    /**
     * Creates a cloud service
     * https://msdn.microsoft.com/en-us/library/azure/gg441304.aspx
     */
    function addCloudService($data){

        $r = ["success" => false];
        try{
            $body = '<?xml version="1.0" encoding="utf-8"?>
                        <CreateHostedService xmlns="http://schemas.microsoft.com/windowsazure">
                          <ServiceName>'.$data['name'].'</ServiceName>
                          <Label>'.$data['base64label'].'</Label>                          
                          <Location>'.$data['location'].'</Location>                                                                          
                        </CreateHostedService>';                        
            $response = $this->client->post("services/hostedservices",array("body" => $body));
            if($response->getStatusCode() == "201"){ 
                $r['success'] = true;            
            }            
            $r['status_code'] = $response->getStatusCode();
            
        }catch (RequestException $e) {
            // $r['request'] = $e->getRequest();
            if ($e->hasResponse()) {
                    $r['response'] = (string)$e->getResponse()->getBody();     
            }
        }        
        return $r;
    }

    /**
     *  Checks if a cloud services exists
     */
    function checkCloudService($name){

        $r = ["success" => false];
        try{
            $response = $this->client->get("services/hostedservices/".$name);
            if($response->getStatusCode() == "200"){ 
                $r['success'] = true;            
            }

            $r['status_code'] = $response->getStatusCode();                        

        }catch (RequestException $e) {
            // $r['request'] = $e->getRequest();
            if ($e->hasResponse()) {
                $r['response'] = (string)$e->getResponse()->getBody();     
            }
        }
        return $r;
    }

    /**
     *  Shutsdown a VM role
     *  https://msdn.microsoft.com/en-us/library/azure/jj157195.aspx
     */

    function shutdownVMRole($vmrole){
        
        $body = '<ShutdownRoleOperation xmlns="http://schemas.microsoft.com/windowsazure" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                  <OperationType>ShutdownRoleOperation</OperationType>
                  <PostShutdownAction>StoppedDeallocated</PostShutdownAction>
                </ShutdownRoleOperation>';
        $r = ["success" => false];
        try{
            $response = $this->client->post("services/hostedservices/".$this->cloudServiceName."/deployments/".$this->deploymentName."/roleinstances/".$vmrole."/Operations",array("body" => $body));
            if($response->getStatusCode() == "202"){
                $r['success'] = true;            
            }
        }catch (RequestException $e) {
            // $r['request'] = $e->getRequest();
            if ($e->hasResponse()) {
                $r['response'] = (string)$e->getResponse()->getBody();     
            }
        }
        return $r;
    }


    /**
     *  Starts a VM role
     *  https://msdn.microsoft.com/en-us/library/azure/jj157189.aspx
     */

    function startVMRole($vmrole){
        
        $body = '<StartRoleOperation xmlns="http://schemas.microsoft.com/windowsazure" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                    <OperationType>StartRoleOperation</OperationType>
                </StartRoleOperation>';
        $r = ["success" => false];
        try{
            $response = $this->client->post("services/hostedservices/".$this->cloudServiceName."/deployments/".$this->deploymentName."/roleinstances/".$vmrole."/Operations",array("body" => $body));
            if($response->getStatusCode() == "202"){
                $r['success'] = true;            
            }
        }catch (RequestException $e) {
             $r['request'] = $e->getRequest();
            if ($e->hasResponse()) {
                $r['response'] = (string)$e->getResponse()->getBody();     
            }
        }
        return $r;
    }

    



}//end of class
