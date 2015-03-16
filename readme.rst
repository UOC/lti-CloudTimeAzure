****************************************
Windows Azure LTi Cloudtime
****************************************

Windows azure LTI Cloudtime let's you manage your Virtual Machines from Windows Azure, from here you can  list, add, start, stop/shutdown, delete and  assign  students from LTI to a Virtual Machine.
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
 * Composer


Configuration
^^^^^^^^^^^^^
Database
""""""""
  Database configuration file is found in application/config/database.php
  for more info about this please check `codeigniter <http://www.codeigniter.com/userguide3>`_

Windows Azure
"""""""""""""
 We need a Windows Azure subscription ID to use the API as well as an storage account that  you must create in advance from Windows Azure control panel and two certificated required to use the API, you can see here how to create the certificates `Create azure PEM and CER files <http://azure.microsoft.com/en-us/documentation/articles/cloud-services-php-how-to-use-service-management/#Connect>`_

 Then you must fill in application/config/constants.php the following:
  
 * AZURE_SUBSCRIPTION_ID = "here you put your subscription id"
 * AZURE_MEDIALINK   = "here goes the whole url to your storage ex. https://YOURSTORAGEID.blob.core.windows.net/";
 * AZURE_CERTIFICATE = "here you must set the path to the location of the PEM certificate" 

LTI Tool Provider
"""""""""""""""""

 We need to insert our consumer key and secret in the table lti_consumer manually, for any trouble with this please check Stephen P Vickers LTI_Tool_Provider documentation.
 
 To access the application we need to set up LTI on our tool consumer to point to www.ourdomain.com/index.php/ltiprovider, once we are validated it will check the user and if this user exists in our database  if not it will create it and then  create the session and redirect the user to his application section.

Translation
"""""""""""
 The translation files are located at appliction/language , for now there is only english, the language is set on a session variable $this->session->language which is set to the language that the user that comes from the LTI Consumer sends us.

OS Images
"""""""""
 In order to avoid having to load all the available OS images all the time from the API, we can import all the OS Images into out database for faster access, this is done by calling the function index.php/manage/import_osimages once or anytime you feel it should be updated.

Application
^^^^^^^^^^^^^^^^^^^^^^
Teachers/Admins
"""""""""""""""
  Once inside the application teachers can add, delete, start, stop and assign available virtual machines to students.

Students
""""""""
 Students will only be able to see their assigned virtual machines and how to access them, if is windows it will be with a remote desktop and if linux then it will be ssh.


TODO 
^^^^
 * Right now it only lets you add 1 Virtual Machine at a time.
 * Only Linux virtual machines are available to create and assign.
 * more testing :)


 




  

 



   
 