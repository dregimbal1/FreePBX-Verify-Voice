<?php

/*

	David Regimbal (c) 2015

*/

out("Installing VerifyVoice");

# Create MySQL table;
$sql = "CREATE TABLE IF NOT EXISTS verifyvoice (
		callerid varchar(25) NOT NULL PRIMARY KEY,
		companyid int(11) NOT NULL,
		countrycode int(11) NOT NULL,
		pin varchar(4) NOT NULL
	)";
$check = $db->query($sql);
if (DB::IsError($check))
{
	die_freepbx( "Could not create verifyvoice: ".$sql." - ".$check->getMessage() .  "<br>");
}

# Register FeatureCode;
$fcc = new featurecode('verifyvoice', 'verifyvoice');
$fcc->setDescription('Verify - Voice');
$fcc->setDefault('*93');
$fcc->update();
unset($fcc);

?>

