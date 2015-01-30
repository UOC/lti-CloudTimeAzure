<?php
/**
 * A Windows Azure helper library for CodeIgniter
 *
 * @author UOC 2015 - Victor Castillo ( victor@tresipunt.com )
 * 
 * 
 */

defined('BASEPATH') OR exit('No direct script access allowed');

use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\ServiceManagement\Models\CreateServiceOptions;



class Azure {

	var $connectionString;

    function __construct(){              
        // $this->CI =& get_instance();
        $this->connectionString =  "SubscriptionID=".AZURE_SUBSCRIPTION_ID.";CertificatePath=".AZURE_CERTIFICATE."";        

    }

    function serviceManagement(){  

	    try{
		    $serviceManagementRestProxy = ServicesBuilder::getInstance()->createServiceManagementService($this->connectionString);
		    $result = $serviceManagementRestProxy->listLocations();
		    $locations = $result->getLocations();

		    foreach($locations as $location){
		          echo $location->getName()."<br />";
		    }
		}
		catch(ServiceException $e){
		    // Handle exception based on error codes and messages.
		    // Error codes and messages are here: 
		    // http://msdn.microsoft.com/en-us/library/windowsazure/ee460801
		    $code = $e->getCode();
		    $error_message = $e->getMessage();
		    echo $code.": ".$error_message."<br />";
		}  	
    	// $serviceManagementRestProxy = ServicesBuilder::getInstance()->createServiceManagementService($this->connectionString);

    	// $result = $serviceManagementRestProxy->listLocations();
    	// $locations = $result->getLocations();

	    // foreach($locations as $location){
	    //       echo $location->getName()."<br />";
	    // }

    }


    function createCloudService(){
	    try{
		    // Create REST proxy.
		    $serviceManagementRestProxy = ServicesBuilder::getInstance()->createServiceManagementService($this->connectionString);

		    $name = "myhostedservice";
		    $label = base64_encode($name);
		    $options = new CreateServiceOptions();
		    $options->setLocation('West Europe');
		    // Instead of setLocation, you can use setAffinityGroup
		    // to set an affinity group.

		    $result = $serviceManagementRestProxy->x($name, $label, $options);
		    echo $result;
		}
		catch(ServiceException $e){
		    // Handle exception based on error codes and messages.
		    // Error codes and messages are here: 
		    // http://msdn.microsoft.com/en-us/library/windowsazure/ee460801
		    $code = $e->getCode();
		    $error_message = $e->getMessage();
		    echo $code.": ".$error_message."<br />";
		}
	}

	public function listHostedServices(){


		try{
			$serviceManagementRestProxy = ServicesBuilder::getInstance()->createServiceManagementService($this->connectionString);
		    $listHostedServicesResult = $serviceManagementRestProxy->listHostedServices();

			$hosted_services = $listHostedServicesResult->getHostedServices();

			foreach($hosted_services as $hosted_service){
			    echo "Service name: ".$hosted_service->getName()."<br />";
			    echo "Management URL: ".$hosted_service->getUrl()."<br />";
			    echo "Affinity group: ".$hosted_service->getAffinityGroup()."<br />";
			    echo "Location: ".$hosted_service->getLocation()."<br />";
			    echo "------<br />";
			}
		    
		}
		catch(ServiceException $e){
		    // Handle exception based on error codes and messages.
		    // Error codes and messages are here: 
		    // http://msdn.microsoft.com/en-us/library/windowsazure/ee460801
		    $code = $e->getCode();
		    $error_message = $e->getMessage();
		    echo $code.": ".$error_message."<br />";
		}
	}
}
