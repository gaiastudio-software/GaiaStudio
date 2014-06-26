<?php
// file:	settings.php
// helps:	many
session_start();
ini_set('html_errors', 0);
require "constants.php";
//if (! isset ($_SESSION ['user_id'])) 
//	header ('Location: http://'.App_Host);   // TODO: Resurrect?

function symbol_count_limit() { return $_SESSION ['symbol_count_limit']; } 

function symbolizing()  {	return  $_SESSION ['symbolizing']; } 

//function filling()  { return  true; } // not yet used

function unique_name()
/*{return "mf_test";}*/
{
	return $_SESSION ['user_id'].'_'.$_SESSION ['epoch'];
}

function zone_table() { return Temp_Sch.'.'.'"zone_'.unique_name().'"'; } // enclose name in quotes as user_id can include dashes, etc
function sect_table() { return Temp_Sch.'.'.'"sect_'.unique_name().'"'; }
function extr_table() { return Temp_Sch.'.'.'"extr_'.unique_name().'"'; }
function feature_kml_path ($fid) { return KML_Folder."/".Feature_prefix.$fid.KML_Ext; }

//function tree_height_incr() { return 5; }// not yet used

function ini_long()  { return -122; }
function ini_lat()   { return   50; }
function ini_range() { return 1000000; }

$look_altitude = 10;
function look_heading() { return  0; }
function look_tilt()    { return 25; }
function doc_range_factor() { return 2; }
function pol_range_factor() { return 1; }

function tree_sym_lod() { return  9; } 
//$polygon_lod =  512; // not yet used
function labeling_document()   { return  true; }
function describing_document() { return  true; }
function labeling_features()   { return  false; }
function describing_features() { return  false; }
function labeling_species()    { return  true; }
function describing_species()  { return  true; }

function model_height() { return 10; }

function fill_opacity() { return "80"; } 
function line_opacity() { return "d0"; }
function line_width()   { return 1; }

function stand_fill_BGR() { return "007070"; }  
function stand_line_BGR() { return "00f000"; }

function water_fill_BGR() { return "700000"; }
function water_line_BGR() { return "f00000"; }

function unrep_fill_BGR() { return "707070"; }
function unrep_line_BGR() { return "f0f0f0"; }

function bare_fill_BGR() { return ""; }
function bare_line_BGR() { return "ffffff"; }

function build_years()
{
	$get_years_sql = "SELECT 
		array_to_json (year[".Yr_index_beg.": ".Yr_index_end."]) AS year 
		FROM ".Yield_Table." LIMIT 1";
	$tab = pg_query (vri_connection(), $get_years_sql) or die ("get_years_sql failed");
	$arr = pg_fetch_array ($tab) or die ("No years!");
	$_SESSION ['year_arr'] = json_decode ($arr['year']);
}

function build_variable_products()
{
	$_SESSION ['variable_product'] = array( 
		"age"  => "stems_age",
		"dhgt" => "stems_dhgt",
		"lhgt" => "stems_lhgt",
		"dia"  => "stems_dia",
		"ba"   => "area_ba",
		"tph"  => "area_tph",
		"vws"  => "area_vws",
		"vcu"  => "area_vcu",
		"vd"   => "area_vd",
		"vdw"  => "area_vdw",
		"vdwb" => "area_vdwb",
	);
}
function variable_product ($variable) { return $_SESSION ['variable_product'][$variable]; }

function build_variable_weights()
{
	$_SESSION ['variable_weight'] = array( 
		"age"  => "live_stems",
		"dhgt" => "live_stems",
		"lhgt" => "live_stems",
		"dia"  => "live_stems",
		"ba"   => "area_ha",
		"tph"  => "area_ha",
		"vws"  => "area_ha",
		"vcu"  => "area_ha",
		"vd"   => "area_ha",
		"vdw"  => "area_ha",
		"vdwb" => "area_ha",
	);
}
function variable_weight ($variable) { return $_SESSION ['variable_weight'][$variable]; }

