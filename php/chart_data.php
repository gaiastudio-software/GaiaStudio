<?php
// file:	chart_data.php
// helps:	chart_creation.js: create_chart()

require "settings.php";
require "db_connection.php";

$featureId = isset ($_REQUEST['featureId']) ? $_REQUEST['featureId'] : null;
$factor =    isset ($_REQUEST['variable']) ?  $_REQUEST['variable'] : null;

$querySimu = "SELECT 
array_to_json (".$factor."[".Yr_index_beg.": ".Yr_index_end."]) AS ".$factor.", 
array_to_json (year[".Yr_index_beg.": ".Yr_index_end."]) AS year 
FROM data.vdyp WHERE feature_id =".$featureId;

$resSimu = pg_query (vri_connection(), $querySimu) or die ("  resSimu sql query failed");
$rowSimu = pg_fetch_array ($resSimu) ; // or die ("  Not enough data for simulation!");

$yearArray =   json_decode ($rowSimu['year']);
$factorArray = json_decode ($rowSimu[$factor]);

$line = '[';
for ($i = 0; $i < count ($yearArray) - 1; $i++) {
	if ($factorArray[$i])
		$line = $line.'['.$yearArray[$i].','.$factorArray[$i].'],';
}
$line = $line.'['.$yearArray[count ($yearArray) - 1].','.$factorArray[count ($yearArray) - 1].']]';
echo $line;

