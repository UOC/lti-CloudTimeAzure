****************************************
Windows Azure LTi Cloudtime
****************************************

Windows azure LTI Cloudtime let's you manage your Virtual Machines from Windows Azure, from here you can  list, add, start, stop/shutdown, delete and  assign to students from LTI to a Virtual Machine.
You can select from a list of public of OS images from Azure or select the one's you have created.

We use `LTI  <http://www.imsglobal.org/toolsinteroperability2.cfm>`_
so it can be integrated into any system that supports LTI, like Moodle or Wordpress.

For this project we are using:
 * Codeigniter 3 RC2
 * Stephen P Vickers `LTI_Tool_Provider <http://www.spvsoftwareproducts.com/php/lti_tool_provider/>`_
 * Guzzle PHP HTTP client ( which is installed via composer )
 * Bootstrap 3

Requirements
^^^^^^^^^^^^
 * PHP 5.2.4+
 * MYSQL 5+
 * Windows Azure subscription ID


Configuration
^^^^^^^^^^^^^
Database
""""""""
  Database configuration file is found in application/config/database.php
  for more info about this please check `codeigniter <http://www.codeigniter.com/userguide3>`_

Windows Azure
"""""""""""""
  We need a Windows Azure subscription ID to use the API as well as an storage account that  you must create in advance from Windows Azure control panel and two certificated required to use the API, you can see here how to create the certificates here `Create azure PEM and CER files <http://azure.microsoft.com/en-us/documentation/articles/cloud-services-php-how-to-use-service-management/#Connect>`_

  Then you must fill in application/config/constants.php the following constants:
  
 * AZURE_SUBSCRIPTION_ID = "here you put your subscription id"
 * AZURE_MEDIALINK   = "here goes the whole url to your storage ex. https://YOURSTORAGEID.blob.core.windows.net/";
 * AZURE_CERTIFICATE = "here you must set the path to the location of the PEM certificate" 



   
 