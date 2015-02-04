<?php

use GuzzleHttp\Client;

class azureRestClient{

    var $client;
    var $azure_subscription_id;
    var $azure_certificate;
    function __construct(){              
         $this->doConnect();            
    }

    function doConnect(){
        $this->client = new GuzzleHttp\Client(
            array('base_url' => 'https://management.core.windows.net/'.AZURE_SUBSCRIPTION_ID."/" ,
                  'defaults' => array(
                        'headers' => array('x-ms-version' => '2014-05-01'),
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