<?php

/*

	David Regimbal (c) 2015

*/
require(dirname(dirname(__FILE__)) . '/admin/modules/whmcsverify_voice/functions.inc.php');
# Include bootstrap;
# Include FreePBX bootstrap settings;
$bootstrap_settings['freepbx_auth'] = false;
if (!@include_once(getenv('FREEPBX_CONF') ? getenv('FREEPBX_CONF') : '/etc/freepbx.conf')) {
	include_once('/etc/asterisk/freepbx.conf');
}

/*

	Any public website can access this page. POST to it. Sanatize to it. Authorize it.

	Then, add it to the database and generate the call file

*/

# Variables
$call_data = array(
	'callerid' => $_POST['callerid'],
	'companyid' => $_POST['companyid'],
	'countrycode' => $_POST['countrycode'],
	'pin' => $_POST['pin']
);

# Send to the database
$sql = "INSERT INTO verifyvoice (callerid,companyid,countrycode,pin) VALUES (
		'" . $call_data['callerid'] . "',
		".$call_data['companyid'].",
		".$call_data['countrycode'].",
		'".$call_data['pin']."'
		) ON DUPLICATE KEY UPDATE companyid = VALUES(companyid), countrycode = VALUES(countrycode), pin = VALUES(pin)";
sql($sql);

# Create call file
$call_file = array(
	'time' => time(),
	'ext' => $_POST['countrycode'] . $call_data['callerid'],
	'maxretries' => 2,
	'retrytime' => 15,
	'waittime' => 20,
	'callerid' => $_POST['countrycode'] . $call_data['callerid'] . " <" . $_POST['countrycode'] . $call_data['callerid'] . ">",
	'application' => 'AGI',
	'data' => 'verifyvoice.php',
	);
	
verifyvoice_gencall($call_file);

echo "Calling...";

