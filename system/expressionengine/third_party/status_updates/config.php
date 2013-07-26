<?php

if ( ! defined('STATUS_UPDATES_ADDON_NAME'))
{
	define('STATUS_UPDATES_ADDON_NAME',         'Status Updates');
	define('STATUS_UPDATES_ADDON_VERSION',      '1.0.1');
}

$config['name'] = STATUS_UPDATES_ADDON_NAME;
$config['version'] = STATUS_UPDATES_ADDON_VERSION;

$config['nsm_addon_updater']['versions_xml'] = 'http://www.intoeetive.com/index.php/update.rss/150';