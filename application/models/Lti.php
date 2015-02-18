<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Lti extends CI_Model {
    

    public function __construct()
    {
            // Call the CI_Model constructor
            parent::__construct();          
    }

       
    /**
     * Checks if the consumer info is on consumer_info table
     */
    function getConsumer($consumer_key,$context_id){
        $result = $this->db->get_where("consumer_info",array("consumer_key" => $consumer_key,"context_id" => $context_id));
        return $result->row_array();
    }
            
    /**
     * Add a new consumer to consumer_info
     */
    function addConsumer($consumer_key,$context_id){

        $insert = array();
        $insert['consumer_key']  = $consumer_key;
        $insert['context_id']  = $context_id;
        if($this->db->insert("consumer_info",$insert)){
            return $this->db->insert_id();
        }else
            return false;            
    }
    


}//end of class