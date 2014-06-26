<?php
// file:	db_connection.php
// helps:	many

function users_connection()
{
	$connection = pg_connect ('host='.Our_Site.' dbname='.Users_DB.' user='.DB_User.' password='.DB_Pass);
	if (! $connection) die ("Could not connect to users database");
	return $connection;
}

//function ()
function vri_connection()
{
	$connection = pg_connect ('host='.DB_Host.' port='.DB_Port.' dbname='.VRI_DB.' user='.DB_User.' password='.DB_Pass);
//	$connection = pg_connect ('host='.Our_Site.' dbname='.VRI_DB.' user='.DB_User.' password='.DB_Pass);
	if (! $connection) die ("Could not connect to VRI database");
	return $connection;
}
