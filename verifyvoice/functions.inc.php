<?php

/*

	David Regimbal (c) 2015

*/

function verifyvoice_get_config($engine) {
	
	$modulename = 'verifyvoice';

	# This will actually put our extn in the dialplan;
	
	global $ext;
	global $asterisk_conf;
	switch($engine) {
		case "asterisk":
			if (is_array($featurelist = featurecodes_getModuleFeatures($modulename))) {
				foreach($featurelist as $item) {
					$featurename = $item['featurename'];
					$fname = $modulename.'_'.$featurename;
					if (function_exists($fname)) {
						$fcc = new featurecode($modulename, $featurename);
						$fc = $fcc->getCodeActive();
						unset($fcc);

						if ($fc != '')
							$fname($fc);
					} else {
						$ext->add('from-internal-additional', 'debug', '', new ext_noop($modulename.": No func $fname"));
					}
				}
			}
		break;
	}
	
}


function verifyvoice_verifyvoice($c) {
	
	// Without this you get no dialplan! xD
	
	global $ext;
	global $asterisk_conf;

	# context;
	$id = "app-verifyvoice";

	$ext->addInclude('from-internal-additional', $id);
	$ext->add($id, $c, '', new ext_Macro('user-callerid'));
	$ext->add($id, $c, '', new ext_answer(''));
	$ext->add($id, $c, '', new ext_wait(1));
	$ext->add($id, $c, '', new ext_agi('verifyvoice.php'));
	$ext->add($id, $c, '', new ext_Hangup);
	
}


function verifyvoice_gencall($call) {
	
	# Fire her up! Schedule a call;

	if ($call['tempdir'] == "") {
		$call['tempdir'] = "/var/spool/asterisk/tmp/";
	}
	if ($call['outdir'] == "") {
		$call['outdir'] = "/var/spool/asterisk/outgoing/";
	}
	if ($call['filename'] == "") {
		$call['filename'] = "wvv.ext.".$call['ext'].".call";
	}

	$tempfile = $call['tempdir'].$call['filename'];
	$outfile = $call['outdir'].$call['filename'];

	# Delete any old .call file with the same name as the one we are creating;
	if( file_exists( "$callfile" ) )
	{
		unlink( "$callfile" );
	}

	# Create up a .call file, write and close;
	$vw = fopen( $tempfile, 'w');
	fputs( $vw, "channel: Local/".$call['ext']."@from-internal\n" );
	fputs( $vw, "maxretries: ".$call['maxretries']."\n");
	fputs( $vw, "retrytime: ".$call['retrytime']."\n");
	fputs( $vw, "waittime: ".$call['waittime']."\n");
	fputs( $vw, "callerid: ".$call['callerid']."\n");
	fputs( $vw, "application: ".$call['application']."\n");
	fputs( $vw, "data: ".$call['data']."\n");
	fclose( $vw );

	# Set the time of this temp file and move to outgoing;
	touch( $tempfile, $call['time'], $call['time'] );
	rename( $tempfile, $outfile );

}