<?php
// file:	write_age_table.php
// helps:	attribute_query.js: field_query_database()

require "settings.php";
require "db_connection.php";
$min_live_stems = 222; 
$min_tree_height = 5; 

$forestage = isset ($_REQUEST['age']) ? $_REQUEST['age'] : null;
$query = "SELECT 
	proj_age_1 as \"Age\", 
	feature_id as \"Feature ID\", 
	species_cd_1 as \"Leading Species Type\", 
	species_cd_2 as \"Second Species Type\", 
	species_pct_1 as \"Leading Species Pct\",
	species_pct_2 as \"Second Species Pct\",
	proj_height_1 as \"Leading Species Height (m)\",
	proj_height_2 as \"Second Species Height (m)\", 
	(polygon_area * vri_live_stems_per_ha)::integer as \"Total Trees\"
FROM data.vi
WHERE true
	AND vri_live_stems_per_ha * polygon_area >= $min_live_stems
	AND proj_height_1 > $min_tree_height AND proj_height_2 > $min_tree_height
	AND	proj_age_1 = ".$forestage." AND	proj_age_2 = ".$forestage."
ORDER BY (polygon_area * vri_live_stems_per_ha)"; 

$res = pg_query (vri_connection(), $query) or die ("sql query failed");
$i = pg_num_fields ($res);

echo "<table class='data'> <thead> <tr>";
for ($j = 0; $j < $i; $j++) {
	$fieldname = pg_field_name ($res, $j);
	echo "<th>" . $fieldname . "";
}
echo "</thead>
	<tbody>";

$id = 0;
while ($row = pg_fetch_array ($res)) {
	$id += 1;
	echo "<tr><td>$row[Age]
	<td><a id='$id' href='#' onclick='query_by_fid ($row[1])'>$row[1]</a>
	<td>$row[2]<td>$row[3]<td>$row[4]<td>$row[5]<td>$row[6]<td>$row[7]<td>$row[8]</tr>";
}
echo "</tbody> </table>";
