<?php

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
         $this->cloudServiceName = CLOUDSERVICE;            
         $this->deploymentName = CLOUDSERVICE;
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
     * returns an array with all available vm images
     */
    function getVmImages(){
        $response = $this->client->get("services/vmimages");
        $result = $response->xml();
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        // return $result;
    }

    /**
     * Adds a new VM to windows azure
     * https://msdn.microsoft.com/en-us/library/azure/jj157186.aspx
     */
    function addVM($add){
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
        // $request = $this->client->createRequest('post','/services/hostedservices/'.$this->cloudServiceName.'/deployments/'.$this->deploymentName.'/roles');
        // $request->setBody(GuzzleHttp\Stream\Stream::factory($body));
        // $response = $this->client->send($request);

        
        
    }

    /**
     * Return all the deployments for a cloud service slot ( production or staging ) 
     */
        function getDeployments(){

            $response = $this->client->get("services/hostedservices/cs-azure-lti-2/deploymentslots/production");
            $xml = $response->xml();
            echo "<pre>";
            print_r($xml);
            echo "</pre>"; 
            https://management.core.windows.net/<subscription-id>/services/hostedservices/<cloudservice-name>/deploymentslots/<deployment-slot>

        }

    /**
     * Return info about a vm instance
     */
    function getVM($cloudname='',$deploymentname='',$rolename=''){
        $cloudname = 'lticloudvms';
        $deploymentname ='lticloudvms';
        $rolename ='lticloudvms';

        $response = $this->client->get("services/hostedservices/".$cloudname."/deployments/".$deploymentname."/roles/".$rolename);
        $xml = $response->xml();
        echo "<pre>";
        print_r($xml);
        echo "</pre>";  
    }
}