<?php

/*
=====================================================
 Status Updates
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2012 Yuri Salimovskiy
=====================================================
 This software is intended for usage with
 ExpressionEngine CMS, version 2.0 or higher
=====================================================
 File: upd.status_updates.php
-----------------------------------------------------
 Purpose: Manage status messages for users
=====================================================
*/

if ( ! defined('BASEPATH'))
{
    exit('Invalid file request');
}

require_once PATH_THIRD.'status_updates/config.php';

class Status_updates_upd {

    var $version = STATUS_UPDATES_ADDON_VERSION;
    
    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
    } 
    
    function install() { 
  
		$this->EE->load->dbforge(); 
        
        //----------------------------------------
		// EXP_MODULES
		// The settings column, Ellislab should have put this one in long ago.
		// No need for a seperate preferences table for each module.
		//----------------------------------------
		if ($this->EE->db->field_exists('settings', 'modules') == FALSE)
		{
			$this->EE->dbforge->add_column('modules', array('settings' => array('type' => 'TEXT') ) );
		}
        
        $settings = array();

        $data = array( 'module_name' => 'Status_updates' , 'module_version' => $this->version, 'has_cp_backend' => 'n', 'has_publish_fields' => 'n', 'settings'=> serialize($settings) ); 
        $this->EE->db->insert('modules', $data); 
        
        $data = array( 'class' => 'Status_updates' , 'method' => 'post_update' ); 
        $this->EE->db->insert('actions', $data); 
        
        //exp_status_updates
        $fields = array(
			'message_id'		=> array('type' => 'INT',		'unsigned' => TRUE, 'auto_increment' => TRUE),
			'member_id'			=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'message_date'		=> array('type' => 'INT',	    'unsigned'=> TRUE,  'default' => 0),
			'message_text'		=> array('type' => 'TEXT',		'default' => '')
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('message_id', TRUE);
		$this->EE->dbforge->add_key('member_id');
		$this->EE->dbforge->create_table('status_updates', TRUE);

        
        return TRUE; 
        
    } 
    
    function uninstall() { 

        $this->EE->db->select('module_id'); 
        $query = $this->EE->db->get_where('modules', array('module_name' => 'Status_updates')); 
        
        $this->EE->db->where('module_id', $query->row('module_id')); 
        $this->EE->db->delete('module_member_groups'); 
        
        $this->EE->db->where('module_name', 'Status_updates'); 
        $this->EE->db->delete('modules'); 
        
        $this->EE->db->where('class', 'Status_updates'); 
        $this->EE->db->delete('actions'); 
        
        return TRUE; 
    } 
    
    function update($current='') 
	{ 
        if ($current < 1.1) 
        { 
           
        } 
        return TRUE; 
    } 
	

}
/* END */
?>