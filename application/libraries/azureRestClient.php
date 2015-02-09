<?php defined('BASEPATH') OR exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Stream\StreamInterfaceuse;
use GuzzleHttp\Exception\RequestException;

class azureRestClient{

    var $client;
    var $azure_subscription_id;
    var $azure_certificate;
    var $cloudServiceName;
    var $deploymentName;

    function __construct(){              
         $this->doConnect();
         $this->cloudServiceName = 'cs-azure-lti-2';            
         $this->deploymentName = 'Staging';
    }

    function doConnect(){
        $this->client = new GuzzleHttp\Client(
            array('base_url' => 'https://management.core.windows.net/'.AZURE_SUBSCRIPTION_ID."/" ,
                  'defaults' => array(
                        'headers' => array('x-ms-version' => '2014-05-01','Content-Type' => 'application/xml'),
                        'cert' => AZURE_CERTIFICATE                        
                   )
            )
            );       
    }

    /**
     * Lista all the locations available
     */
    function listLocations(){
        $response =  $this->client->get('locations');
        $xml = $response->xml();
        echo "<pre>";
        print_r($xml);
        echo "</pre>";        
    }

    /**
     * Returns all windows azure CloudServices for the subscription
     */
    function getCLoudServices(){

        $response = $this->client->get("services/hostedservices");
        $result = $response->xml();
        $return = array();        
        if(!empty($result)){
            $a = 0;
            foreach($result->HostedService as $k => $v){                
                $return[$a]['name'] = (string)$v->ServiceName;
                $return[$a]['location'] = (string)$v->HostedServiceProperties->Location;
                $return[$a]['status'] = (string)$v->HostedServiceProperties->Status;
                $a++;
            }
        }
        return $return;
    }


    /**
     * returns an array with all available OS Images
     */
    function getOSImages(){
        $return = [];
        try {
            $response = $this->client->get("services/images");
            $return = $response->xml();            
            }catch (RequestException $e) {
                echo "<pre>";
                echo $e->getRequest() . "\n";
                if ($e->hasResponse()) {
                    echo $e->getResponse() . "\n";
                }
                echo "</pre>";
            }            
         return $return;
    }

    /**
     * Adds a new VM to windows azure
     * https://msdn.microsoft.com/en-us/library/azure/jj157186.aspx
     */
    function addVMDeployment($add){
        $this->getDeployments();

        // $this->getVmImages();             
        /* workding xml to making a deployment on a cloudservice , a cloudservice must be created beforehand */ 
        //name of deplyment = staging
        // $body ='<Deployment xmlns="http://schemas.microsoft.com/windowsazure" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
        //           <Name>Staging</Name>
        //           <DeploymentSlot>Production</DeploymentSlot>
        //           <Label>stk_curl_label_144</Label>
        //           <RoleList>
        //             <Role>
        //               <RoleName>stk_curl_role_1</RoleName>
        //               <RoleType>PersistentVMRole</RoleType>
        //               <ConfigurationSets>
        //                 <ConfigurationSet i:type="LinuxProvisioningConfigurationSet">
        //                   <ConfigurationSetType>LinuxWindowsProvisioningConfiguration</ConfigurationSetType>
        //                   <HostName>vm-linux-1</HostName>                          
        //                   <UserName>azureLTI1_</UserName>
        //                   <UserPassword>azureLTI1_</UserPassword>                                                   
        //                 </ConfigurationSet>
        //               </ConfigurationSets>   
        //               <OSVirtualHardDisk>
        //                 <MediaLink>https://portalvhds71n9prl4byf1b.blob.core.windows.net/vhds/communityimages2.vhd</MediaLink>
        //                 <SourceImageName>LAMP-Stack-5-5-17-0-dev-Ubuntu-14-04</SourceImageName>
        //               </OSVirtualHardDisk>
        //             </Role>
        //           </RoleList>
        //         </Deployment>'; 
        //  $this->client->post('services/hostedservices/cs-azure-lti-2/deployments', array("body" => $body));

                // try{
                //     $this->client->post('services/hostedservices/cs-azure-lti-2/deployments', array("body" => $body));
                // }catch (RequestException $e) {
                //     echo "<pre>";
                //     echo $e->getRequest() . "\n";
                //     if ($e->hasResponse()) {
                //         echo $e->getResponse() . "\n";
                //     }
                //     echo "</pre>";
                // }
        // $request = $this->client->createRequest('post','/services/hostedservices/'   .$this->cloudServiceName.'/deployments/'.$this->deploymentName.'/roles');
        // $request->setBody(GuzzleHttp\Stream\Stream::factory($body));
        // $response = $this->client->send($request);

    }
    function addVMRole(){
        $body ='<PersistentVMRole xmlns="http://schemas.microsoft.com/windowsazure" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                  <RoleName>vm-azure-lti3</RoleName>
                  <RoleType>PersistentVMRole</RoleType>                  
                      <ConfigurationSets>
                        <ConfigurationSet i:type="LinuxProvisioningConfigurationSet">
                          <ConfigurationSetType>LinuxProvisioningConfiguration</ConfigurationSetType>
                          <HostName>vm-linux-3</HostName>
                          <UserName>azureLTI1_</UserName>
                          <UserPassword>azureLTI1_</UserPassword>                                                   
                        </ConfigurationSet>
                      </ConfigurationSets>   
                      <OSVirtualHardDisk>
                        <MediaLink>https://portalvhds71n9prl4byf1b.blob.core.windows.net/vhds/communityimages2.vhd</MediaLink>
                        <SourceImageName>LAMP-Stack-5-5-17-0-dev-Ubuntu-14-04</SourceImageName>
                        <OS>Linux</OS>
                      </OSVirtualHardDisk>                  
                </PersistentVMRole>'; 

            try {
                $response = $this->client->post('services/hostedservices/cs-azure-lti-2/deployments/Staging/roles', array("body" => $body));
                echo "<pre>";
                print_r($response);
                echo "</pre>";

                $xml = $response->xml();
                echo "<pre>";
                print_r($xml);
                echo "</pre>";  

                }catch (RequestException $e) {
                    echo "<pre>";
                    echo $e->getRequest() . "\n";
                    if ($e->hasResponse()) {
                        echo $e->getResponse() . "\n";
                    }
                    echo "</pre>";
            }

    }

    /**
     * Return all the deployments for a cloud service slot ( production or staging ) 
     */
        function getDeployments($slot = "production"){

            $return = [];
            try {
                $response = $this->client->get("services/hostedservices/".$this->cloudServiceName."/deploymentslots/".$slot);
                $xml = $response->xml();                
                    if($xml){
                        foreach($xml as $key => $value){
                            $a = 0;
                            foreach($value->RoleInstance as $ri){

                                $return[$a]['deploymentInfo'] = $ri;
                                $return[$a]['roleInfo'] = $this->getRole($ri->RoleName);
                                $a++;
                            }
                        }            
                    }
                }catch (RequestException $e) {
                    echo "<pre>";
                    echo $e->getRequest() . "\n";
                    if ($e->hasResponse()) {
                        echo $e->getResponse() . "\n";
                    }
                    echo "</pre>";
            }

            return $return;

        }

    /**
     * Return info about a vm role 
     * https://msdn.microsoft.com/en-us/library/azure/jj157193.aspx
     */
    function getRole($rolename = ''){
        
        $return = "";
        try{
            $response = $this->client->get("services/hostedservices/".$this->cloudServiceName."/deployments/".$this->deploymentName."/roles/".$rolename);
            $return = $response->xml();        
        }catch (RequestException $e) {

            echo "<pre>";
            echo $e->getRequest() . "\n";
                if ($e->hasResponse()) {
                    echo $e->getResponse() . "\n";
                }
            echo "</pre>";

        }
        return $return;
    }

}//end of class
