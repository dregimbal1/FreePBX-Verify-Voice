<?php

/*

	David Regimbal (c) 2015

*/

# custom message to display;
print "Verify - Voice is being uninstalled.<br>";

# drop tables;
$sql = "DROP TABLE IF EXISTS verifyvoice";
$check = $db->query($sql);
if (DB::IsError($check)) {
        die_freepbx( "Can not delete `verifyvoice` table: " . $check->getMessage() .  "\n");
}

/*
	 You could also check dir spool/asterisk/outgoing
	 and remove any calls that were not processed

*/