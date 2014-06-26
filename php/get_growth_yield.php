<?php
// file:	get_growth_yield.php
// helps:	chart_creation.js: create_chart()

require "settings.php";
require "db_connection.php";

$variable =  isset ($_REQUEST['variable']) ?  $_REQUEST['variable'] : null;

switch ($_SESSION['query_type']) {
case 'box': 
case 'reg': 
	$get_yield_sql = "SELECT 
		array_to_json (
			scale_values (
				1.0 / sum (".variable_weight ($variable)."), array_sum (array_concat (ARRAY [".variable_product($variable)." [".Yr_index_beg.": ".Yr_index_end."]::integer[]]))
			) 
		) AS ".$variable."
		FROM ".extr_table().";";
	break;
case 'fid': 
	$get_yield_sql = "SELECT 
		array_to_json (".$variable."[".Yr_index_beg.": ".Yr_index_end."]) AS ".$variable."
		FROM ".Yield_Table." WHERE feature_id = ".$_SESSION['feature_id'];
	break;
}
$tab = pg_query (vri_connection(), $get_yield_sql) or die ("get_yield_sql failed");
$arr = pg_fetch_array ($tab) ; 
if (count ($arr[$variable]) && $arr[$variable] != "[]") {
	$year_arr = $_SESSION ['year_arr'];
	$variable_arr = json_decode ($arr[$variable]);
	$line = '[';
	for ($i = 0; $i < count ($year_arr) - 1; $i++) {
		if ($variable_arr[$i])
			$line = $line.'['.$year_arr[$i].','.$variable_arr[$i].'],';
	}
	$line = $line.'['.$year_arr[count ($year_arr) - 1].','.$variable_arr[count ($year_arr) - 1].']]';
} else
	$line = '';
echo $line;
