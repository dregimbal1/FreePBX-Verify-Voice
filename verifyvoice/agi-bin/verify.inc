<?php

/*

	David Regimbal (c) 2015

*/

# Include FreePBX bootstrap settings;
$bootstrap_settings['freepbx_auth'] = false;
if (!@include_once(getenv('FREEPBX_CONF') ? getenv('FREEPBX_CONF') : '/etc/freepbx.conf')) {
	include_once('/etc/asterisk/freepbx.conf');
}

   	// ------------
	// CONFIG Parms
	// ------------
	$err_log =  $amp_conf[ASTLOGDIR].'/whmcs_verify_voice.log';   
	$debug = 1;	  
	$temp_dir =  $amp_conf[ASTSPOOLDIR].'/tmp';
	if (!is_dir ($parm_temp_dir)) mkdir ($parm_temp_dir, 0775);
	$call_dir = $amp_conf[ASTSPOOLDIR].'/outgoing';
	$maxretries = 2; 
	$waittime = 30;
	$retrytime = 30; 
	$chan_ext = 0;
	$short_entry = 1; 
	// -- Application
	$application = 'AGI';
	$data = 'verifyvoice.php'; 
	//-------------------
	// END CONFIG PARMS
	//-------------------

