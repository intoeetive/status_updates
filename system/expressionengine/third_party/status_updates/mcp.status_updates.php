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
 File: mcp.status_updates.php
-----------------------------------------------------
 Purpose: Manage status messages for users
=====================================================
*/

if ( ! defined('BASEPATH'))
{
    exit('Invalid file request');
}

require_once PATH_THIRD.'status_updates/config.php';

class Status_updates_mcp {

    var $version = STATUS_UPDATES_ADDON_VERSION;
    
    var $settings = array();
    
    var $perpage = 25;

    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
    } 
    
    
    function index()
    {
        
    }

  

}
/* END */
?>