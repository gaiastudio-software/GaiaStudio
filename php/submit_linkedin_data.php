<?php
// file:	submit_linkedin_data.php
// helps:	linkedin_login.js: submit_linkedin_data()

	require "settings.php";
	require "db_connection.php";
	$table_name = "user_accounts";
	
	$id =         isset ($_REQUEST['id']) ?         $_REQUEST['id'] : null;
	$firstName =  isset ($_REQUEST['firstName']) ?  $_REQUEST['firstName'] : null;
	$lastName =   isset ($_REQUEST['lastName']) ?   $_REQUEST['lastName'] : null;
	$pictureUrl = isset ($_REQUEST['pictureUrl']) ? $_REQUEST['pictureUrl'] : null; 
	$headline =   isset ($_REQUEST['headline']) ?   $_REQUEST['headline'] : null;
	$industry =   isset ($_REQUEST['industry']) ?   $_REQUEST['industry'] : null;
	$publicProfileUrl = isset ($_REQUEST['publicProfileUrl']) ? $_REQUEST['publicProfileUrl'] : null; 
	
	$sql_insert = "INSERT INTO $table_name 
		(id_gaiastudio, id_linkedin, first_name, family_name, picture_url, headline, industry, profile_url)
		VALUES (DEFAULT, '$id', '$firstName', '$lastName', '$pictureUrl', '$headline', '$industry', '$publicProfileUrl')"; 

	$res = pg_query (users_connection(), $sql_insert) or die ("sql_insert failed");

	$sql_select = "SELECT first_name, family_name, picture_url, headline, industry, profile_url FROM $table_name WHERE id_linkedin = '$id' LIMIT 1";

	$res = pg_query (users_connection(), $sql_select) or die ("sql_select failed");
	$user = pg_fetch_array ($res);
	
	echo ($user[0] ? 'true' : 'false');
