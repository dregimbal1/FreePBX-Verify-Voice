<?php
/*

	David Regimbal (c) 2015

*/

$dispnum = 'verifyvoice';

$engineinfo = engine_getinfo();
$astver =  $engineinfo['version'];
$ast_lt_18 = version_compare($astver, '1.8', 'lt');

	if ($ast_lt_18) {
		?>Verify - Voice requires Asterisk 1.8+<?php
        }


	echo "There are no settings for this.";
	
	
	/*
	
		No immediate settings here but feel free to add some.
	
		Think about API information if you are integrating a 3rd party
	
	*/

