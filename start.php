<?php
/***************************************************************************
	*                            TwizaNex Smart Community Software
	*                            ---------------------------------
	*  Start.php:  gutwacaptcha for Elgg 1.8.15
        *	
	*     begin                : Mon Mar 23 2011
	*     copyright            : (C) 2011 TwizaNex Group
	*     website              : http://www.TwizaNex.com/
	* This file is part of TwizaNex - Smart Community Software
	*
	* @package Twizanex
	* @link http://www.twizanex.com/
	* TwizaNex is free software. This work is licensed under a GNU Public License version 2. 
	* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
	* @author Tom Ondiba <twizanex@yahoo.com>
	* @copyright Twizanex Group 2011
	* TwizaNex is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
	* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
	* See the GNU Public License version 2 for more details. 
	* For any questions or suggestion write to write to twizanex@yahoo.com
	***************************************************************************/

elgg_register_event_handler('init', 'system', 'gutwahammer_init');

/**
 * Initialize gutwahammer
 */
function gutwahammer_init() {

	global $CONFIG;

	  elgg_register_plugin_hook_handler('route', 'all', 'gutwa_hammer_plugin_hook_handler'); // works but does not triger

}

/**
 * We will grab the existing $page and forward to sef_page_handler, which will call_user_func the original handler
 */


function gutwa_hammer_plugin_hook_handler($hook, $type, $return, $params) {

	$segments = elgg_extract('segments', $return, '');


	$handler = elgg_extract('handler', $return);

// We don't need sef URLs for the following handlers
	$handler_exceptions = array(
		'action',
		'admin',
		'cache',
		'services',
		'export',
		'mt',
		'xml-rpc',
		'rewrite',
		'tag',
		'pg',
		'admin',
		'cron',
		'js',
		'css',
		'ajax',
		'livesearch',
		'activity',
		'setting',
		'friends',
		'friendsof',
		'forgotpassword',
		'resetpassword',
		'login',
		'avatar',
		'profile',
		'collections',	

);

 if (in_array($handler, $handler_exceptions)) {      
          return $return;
         }
       
  // This is our current url
if (sizeof($segments) > 0) {
$segments_url = '/' . implode('/', $segments);
}
$full_url = "{$handler}{$segments_url}";

include_once(dirname(dirname(__FILE__)) . "/gutwahammer/anti-hammer.php");

// return true;
	
}
