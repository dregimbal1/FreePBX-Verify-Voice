#!/usr/bin/php -q
<?php
/*

	David Regimbal (c) 2015

*/

{
	# clean up the call of gunk;
	ob_implicit_flush(false);
	set_time_limit(30);
	error_reporting(0);
	
	# config options;
	require 'verify.inc';
	GLOBAL	$stdin, $stdout, $stdlog, $result, $debug, $err_log, $test_mode, $db;
	
	# config and start debug;
	$stdin = fopen( 'php://stdin', 'r' );
	$stdout = fopen( 'php://stdout', 'w' );
	if ($debug)
	{
		$stdlog = fopen( $err_log, 'w' );
		fputs( $stdlog, "Verify - Voice by David Regimbal (c) 2015 has started a call \n" );
	}
	
	# asterisk sends some cool stuff to help with development;
	while ( !feof($stdin) ) 
	{
		$temp = fgets( $stdin );

		if ($debug)
			fputs( $stdlog, $temp );

		$temp = str_replace( "\n", "", $temp );

		$s = explode( ":", $temp );
		$agivar[$s[0]] = trim( $s[1] );
		if ( ( $temp == "") || ($temp == "\n") )
		{
			break;
		}
	} 
	
	# takes SIP/123 and splits it up;
	$channel = $agivar[agi_channel];
	if (preg_match('.^([a-zA-Z]+)/([0-9]+)([0-9a-zA-Z-]*).', $channel, $match) )
	{
		$sta = trim($match[2]);
		$chan = trim($match[1]);
	}
	
	# get the caller id;
	$callerid = $agivar['agi_extension'];
	if (preg_match('/<([ 0-9]+)>/', $callerid, $match) )
	{
		$cidn = trim($match[1]);
		if ($debug) {
			fputs( $stdlog, "Caller ID:" . $cidn . "\n" );
		}
	}
	else     # rats.. didnt find it in <###>
	{
		if (preg_match('/([0-9]+)/', $callerid, $match) )
		{
			$cidn = trim($match[1]);
			if ($debug) {
				fputs( $stdlog, "Caller ID:" . $cidn . "\n" );
			}
		}
		else
		{
			$cidn = -1;	 # Well this is embarrassing;
			if ($debug) {
				fputs( $stdlog, "Could not find caller id \n" );
			}
			die();
		}
	}
	
	
	
	# get caller id's pin from the dB
	$sql = "SELECT * FROM verifyvoice WHERE callerid = '" . $cidn . "'";	
	$results_2d = sql($sql,"getAll",DB_FETCHMODE_ASSOC);
	$results_1d = array();
	foreach ($results_2d[0] as $key => $value) {
		$results_1d[$key] = $value;
	}
	
	
	$pin = $results_1d['pin'];
	if ($debug) {
		fputs( $stdlog, "PIN: " . $pin . "\n" );
	}

	
	# greet the caller;
	execute_agi( "STREAM FILE en/beep \"\" ");
	
	# say the pin;
	execute_agi("SAY DIGITS $pin \"\"");
	
	# say goodbye;
	execute_agi( "STREAM FILE en/beep \"\" ");

	# now... GTFO!;
	$rc = execute_agi( "HANGUP ");
	
}


# Kinda like raw command line, but safer because it is Asterisks-specific;
function execute_agi( $command )
{
    GLOBAL	$stdin, $stdout, $stdlog, $debug;

    fputs( $stdout, $command . "\n" );
    fflush( $stdout );
    if ($debug)
        fputs( $stdlog, $command . "\n" );

    $resp = fgets( $stdin, 4096 );

    if ($debug)
        fputs( $stdlog, $resp );

    if ( preg_match("/^([0-9]{1,3}) (.*)/", $resp, $matches) ) 
    {
        if (preg_match('/result=([-0-9a-zA-Z]*)(.*)/', $matches[2], $match)) 
        {
            $arr['code'] = $matches[1];
            $arr['result'] = $match[1];
			
			  fputs( $stdlog, "pin=$matches[1]\n" );	
			
            if (isset($match[3]) && $match[3])
                $arr['data'] = $match[3];
            return $arr;
        } 
        else 
        {
            if ($debug)
                fputs( $stdlog, "Couldn't figure out returned string, Returning code=$matches[1] result=0\n" );	
            $arr['code'] = $matches[1];
            $arr['result'] = 0;
            return $arr;
        }
   	} 
    else 
    {
        if ($debug)
            fputs( $stdlog, "Could not process string, Returning -1\n" );
        $arr['code'] = -1;
        $arr['result'] = -1;
        return $arr;
    }
}