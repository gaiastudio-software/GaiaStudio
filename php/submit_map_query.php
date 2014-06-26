<?php
// file:	submit_map_query.php
// helps:	map_visualization.js: query_database()
session_start();
require "settings.php";
require "db_connection.php";
require "kml_writer.php";
require "html_writer.php";
require "queries.php";

drop_temp_tables(); // previous query results // Must be before session epoch is reset
$_SESSION['query_type'] = isset ($_REQUEST['query_type']) ? $_REQUEST['query_type'] : null;
$_SESSION['feature_id'] = isset ($_REQUEST['feature']) ?    $_REQUEST['feature'] : null;
$zone_kml =    isset ($_REQUEST['zone']) ?        $_REQUEST['zone'] : null;
$map_id =      isset ($_REQUEST['map_id']) ?      $_REQUEST['map_id'] : null;
$user_region = isset ($_REQUEST['user_region']) ? $_REQUEST['user_region'] : null;
$_SESSION['symbolizing'] =       ($_REQUEST['symbolizing'] == "true");
$_SESSION['symbol_count_limit'] = $_REQUEST['symbol_limit'];
$_SESSION['epoch'] = time();
switch ($_SESSION['query_type']) {
case 'box': 
	$query_zone_name = $_SESSION['first_name']."_".$_SESSION ['epoch'];
	$summary = extract_zone_from_main ($zone_kml);
	$file_path = KML_Folder."/".Zone_prefix.$query_zone_name.KML_Ext;
	break;
case "reg":
	$query_zone_name = $user_region."_".$_SESSION ['epoch'];
	$summary = extract_zone_from_main ($zone_kml);
	$file_path = KML_Folder."/".Zone_prefix.$query_zone_name.KML_Ext;
	break;
case 'fid': 
	$summary = summarize_feature ($_SESSION['feature_id']); 
	$file_path = feature_kml_path ($_SESSION['feature_id']); 
	break;
}
if ($summary) {
	$symbolization_factor = symbol_factor ($summary[live_stems_1] + $summary[live_stems_2]);
	$symbol_count =	write_kml_file ($file_path, $summary, $symbolization_factor, $query_zone_name);
	$table_url = write_htm_file ($summary, $query_zone_name);
	echo "OK"; // 0
	$heading = result_text ($note, $summary, $symbol_count, $symbolization_factor, $query_zone_name);
	// TODO: Use proper JSON
	echo '%%%'.$heading; // 1
	echo '%%%'.$note;   // 2
	$map_url = Root_Path."/".$file_path;
	echo '%%%'.'http://'.App_Host.'/'.$file_path; // 3
	echo '%%%'.$summary[polys]; // 4
	echo '%%%'.write_html_tables ($_SESSION['feature_id'], $summary); // 5
	echo '%%%'.$table_url; // 6
	echo '%%%'.$map_url; // 7

//	drop_temp_tables(); // cleanup
//	if ($_SESSION['query_type'] == 'fid')		session_write_close(); // Not sure when to "close"
} else { // ! $summary
	echo "OK"; // 0
	switch ($_SESSION['query_type']) {
	case 'box': 
	case "reg":
		$heading = "Nothing found in the inventory at ".$query_zone_name;
		$note   = "You're probably outside the current inventory coverage or possibly inside a gap within the inventory. ";
		$note  .= "You're still welcome to browse around here though. ";
		break;
	case 'fid':  
		$heading = "Feature ".$_SESSION['feature_id']." not found in the inventory";
		break;
	}
	$note  .= "<p>".VRI_Coverage;
	echo '%%%'.$heading; // 1
	echo '%%%'.$note;   // 2
	echo '%%%';   // 3
}
////////////////////////////////////////////// local helpers  //////////////////////////////////////////////

function extract_zone_from_main ($zone_kml)
{
	make_map_polygon_from_kml_polygon (zone_table(), $zone_kml, Map_SRID);
	//$area = area_from_polygon (zone_table());
	if (intersect_zone()) {
		extract_sect(); 
		return summarize_extract();
	} else
		return null;
}

function	write_kml_file ($file_path, &$summary, $symbolization_factor, $query_zone_name)
{
	$kml_file = fopen ("../".$file_path, "w") or die ('Cannot open KML file'); 
	// TODO: if (! Using_Existing_KML || ! file_exists ($kml_file))
	$description =  summary_to_description ($summary, false);
	write_kml_head ($kml_file);	
	switch ($_SESSION['query_type']) {
	case 'box': 
	case "reg":
		if ($summary[polys] > 1) $summary += condense_extract();
		write_kml_zone_head ($kml_file, $query_zone_name, $description);
		$symbol_count =	write_kml_zone_body ($kml_file, $symbolization_factor);	
		break;
	case 'fid':  
		$symbol_count =	write_kml_feature_body ($kml_file, $symbolization_factor, $_SESSION['feature_id']);	
		break;
	}
	write_kml_foot ($kml_file);	
	return $symbol_count;
}	

function	write_htm_file ($summary, $query_zone_name)
{
	switch ($_SESSION['query_type']) {
	case 'box': 
	case "reg":
		$file_name = Results_Folder."/".Zone_prefix.$query_zone_name.Result_Ext;
		$table_url = Root_Path."/".$file_name;
		$file_resource = fopen ("../".$file_name, "w") or die ('error %%% Cannot open result file');
		write_html_head   ($file_resource, $query_zone_name);
		write_html_tables ($file_resource, $summary);
		write_html_foot   ($file_resource);
	}
	return $table_url;
}

function 	result_text (&$note, $summary, $symbol_count, $symbolization_factor, $query_zone_name)
{
	if ($summary[polys]) {
		switch ($_SESSION['query_type']) {
		case 'box': 
			$heading = 'Query Box "'.$query_zone_name.'"'; 
			break;
		case "reg":
			$heading = 'Query Region "'.$query_zone_name.'"'; 
			break;
		case 'fid':  
			$heading = "Feature ".$_SESSION['feature_id'];
			break;
		}
		$note = "";
		if ($summary[live_stems]) {
			if (symbolizing()) {
				if ($symbol_count) {
					$note .= '<p>3D symbols: '.$symbol_count.'. &nbsp; [Each tree <i>symbol</i> represents ';
					$note .= ($symbolization_factor == 1) ? 
						'one <i>actual</i> sp1 or sp2 tree.]' : $symbolization_factor.' <i>actual</i> sp1 or sp2 trees]. ';
				}
			} else 
						$note .= '<p><em>3D symbolization is turned off.</em>';		
		} else {
			$note .= '<p>Even if you see actual trees, they are not in the inventory. ';
		}
	} else { // ! $summary[polys]
		$heading = "Nothing found in the inventory at ".$query_zone_name;
		$note   = "You're probably outside the current inventory coverage. ";
		$note  .= "You're still welcome to browse around here though. ";
		$note  .= "<p>".VRI_Coverage;
	}
	return $heading;
}

function drop_temp_tables()
{
	drop_table (zone_table());
	drop_table (sect_table());
	drop_table (extr_table());
}

function arc_length_to_angle ($length, $radius) 
{ 
	if ($radius <= 0) die ("error %%% Zero or negative radius passed to arc_length_to_angle()");
	return rad2deg ((float)$length / (float)$radius);
}

function symbol_factor ($stem_count)
{
	return ($stem_count <= symbol_count_limit()) ? 1 : (int)($stem_count / symbol_count_limit() + 1);
}

function summary_to_description ($summary, $marking)
// TODO: Expand to include nominal data
{
	$total_trees = $summary[live_stems] > 0 ? $summary[live_stems] : 0;
	$spacer = $marking ? '. &nbsp; ' : '.  ';
	$description = 'Features: '. $summary[polys].$spacer;
	$description .= 'Pieces: '.  $summary[rings].$spacer;
	$description .= 'Area: '.    $summary[area_ha].' ha'.$spacer;
	$description .= 'Density: '. $summary[density].' stm/ha'.$spacer;
	$description .= 'Trees: '.   $total_trees.$spacer;
	$description .= 'Sp 1 Age: '.$summary[age_1].$spacer;
	$description .= 'Sp 1 Ht: '. $summary[height_1].' m'.$spacer;
	$description .= 'Sp 2 Age: '.$summary[age_2].$spacer;
	$description .= 'Sp 2 Ht: '. $summary[height_2].' m'.$spacer;
	return $description;
}
