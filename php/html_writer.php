<?php
// file:	html_writer.php
// helps:	submit_map_query.php

function write_html_doctype ($f)
// helps:	submit_map_query.php 
{
	$htm = array();
	$htm[] = '<!DOCTYPE html>';
	$htm[] = '<html>';
	$htmOutput = join ("\n", $htm);
	if (gettype ($f) == "resource")
		fwrite ($f, $htmOutput) or die ("Cannot write_html_doctype"); 
	else
		return $htmOutput;
}

function write_html_head ($f, $title)
// helps:	submit_map_query.php 
{
	$htm = array();
	$htm[] = '<!DOCTYPE html>';
	$htm[] = '<html>';
	$htm[] = '<head>';
	$htm[] = '	<meta charset="utf-8">';
	$htm[] = '	<title>'.$title.'</title>';
	// TODO: lang=en   generator=gaiastudio etc
	$htm[] = '	<link type="text/css" rel="stylesheet" href="'.Styles_Href.'/app.css">';
	$htm[] = '</head>';
	$htm[] = '<body>';
	$htmOutput = join ("\n", $htm);
	fwrite ($f, $htmOutput) or die ("Cannot write_html_head"); 
}

function write_html_tables ($f, $summary)
// helps:	submit_map_query.php [twice]
{
	$long_string = "";
	if (gettype ($f) != "string" && $summary[polys] > 1)
		$long_string .= write_html_table ($f, $summary, "precis");
	$long_string .= write_html_table ($f, $summary, "results");
	return $long_string;
}

function write_html_table ($f, $summary, $type)
// helps:	here write_html_tables [twice]
{
	$long_string = "";
	$htm = array('');
	$htm[] = '	<table id="'.$type.'" class="data" style="table-layout: fixed;">';
	$htmOutput = join ("\n", $htm);
	if (gettype ($f) == "resource")
		fwrite ($f, $htmOutput) or die ("Cannot write_html_table ".$type); 
	else
		$long_string .= $htmOutput;
	$long_string .= write_table_head ($f, $type);
	$long_string .= write_table_body ($f, $type, $summary);
	$htm = array('');
	$htm[] = '	</table>';
	$htmOutput = join ("\n", $htm);
	if (gettype ($f) == "resource")
		fwrite ($f, $htmOutput) or die ("Cannot write_html_table ".$type); 
	else
		$long_string .= $htmOutput;
	return $long_string;
}

function write_table_head ($f, $type)
// helps:	here write_html_table() 
{
	$htm = array('');
	$htm[] = '		<thead>';
	$htm[] = '			<tr>';
	$htm[] .= ' <th rowspan="2" style="width: 5em;">Feature          <br>'.($type == "precis" ? 'count' : 'ID');
	$htm[] .= ' <th rowspan="2" style="width: 3em;">Pcs';
	$htm[] .= ' <th rowspan="2" style="width: 5em;">Area             <br>(ha)';
	$htm[] .= ' <th rowspan="2" style="width: 6em;">Density          <br>(stm/ha)';
	$htm[] .= ' <th rowspan="2" style="width: 6em;">Total            <br>(stems)';
	$htm[] .= ' <th rowspan="2" style="width: 5em;">Diam<sub>125</sub><br>(cm)';
	$htm[] .= ' <th rowspan="2" style="width: 5em;">BA               <br>(m&sup2;/ha)';
	$htm[] .= ' <th rowspan="2" style="width: 5em;">Vol<sub>125</sub><br>(m&sup3;/ha)';
	$htm[] .= ' <th rowspan="2" style="width: 6em;">BCGS             <br>map';
	$htm[] .= ' <th rowspan="2" style="width: 4em;">BEC              <br>zone';
	$htm[] .= ' <th rowspan="2" style="width: 6em;">Land             <br>cover';
	$htm[] .= ' <th colspan="5">Species 1';
	$htm[] .= ' <th colspan="5">Species 2';
	$htm[] .= ' <th colspan="3">Sp 3';
	$htm[] .= ' <th colspan="3">Sp 4';
	$htm[] .= ' <th colspan="3">Sp 5';
	$htm[] .= ' <th colspan="3">Sp 6';
	$htm[] = '			<tr>';
	$htm[] .= ' <th style="width: 3em;">Type';
	$htm[] .= ' <th style="width: 3em;">Pct';
	$htm[] .= ' <th style="width: 4em;">Stems';
	$htm[] .= ' <th style="width: 3em;">Age';
	$htm[] .= ' <th style="width: 3em;">Ht(m)';
	$htm[] .= ' <th style="width: 3em;">Type';
	$htm[] .= ' <th style="width: 3em;">Pct';
	$htm[] .= ' <th style="width: 4em;">Stems';
	$htm[] .= ' <th style="width: 3em;">Age';
	$htm[] .= ' <th style="width: 3em;">Ht(m)';
	$htm[] .= ' <th style="width: 3em;">Type';
	$htm[] .= ' <th style="width: 3em;">Pct';
	$htm[] .= ' <th style="width: 4em;">Stems';
	$htm[] .= ' <th style="width: 3em;">Type';
	$htm[] .= ' <th style="width: 3em;">Pct';
	$htm[] .= ' <th style="width: 4em;">Stems';
	$htm[] .= ' <th style="width: 3em;">Type';
	$htm[] .= ' <th style="width: 3em;">Pct';
	$htm[] .= ' <th style="width: 4em;">Stems';
	$htm[] .= ' <th style="width: 3em;">Type';
	$htm[] .= ' <th style="width: 3em;">Pct';
	$htm[] .= ' <th style="width: 4em;">Stems';
	$htm[] = '		</thead>';
	$htmOutput = join ("\n", $htm);
	if (gettype ($f) == "resource")
		fwrite ($f, $htmOutput) or die ("Cannot write_table_head"); 
	else
		return $htmOutput;
}		 

function write_table_body ($f, $type, $summary)
// helps:	submit_map_query.php 
{
	$long_string = "";
	$htm = array('');
	$htm[] = '		<tbody>';
	$htmOutput = join ("\n", $htm);
	if (gettype ($f) == "resource")
		fwrite ($f, $htmOutput) or die ("Cannot write_table_body"); 
	else
		$long_string .= $htmOutput;
		
	if (gettype ($f) == "resource" || gettype ($f) == "NULL")
		$statistics = zone_statistics();
	else
		$statistics = feature_statistics ($f);
	$rows = pg_num_rows ($statistics);
	
	if ($type == "precis")
		$long_string .= write_table_summary ($f, $summary); 
	else 
		for ($i = 0; $i < $rows; $i++) {
			$polygon = pg_fetch_array ($statistics);
			$long_string .= write_table_row ($f, $polygon);
		}
	$htm = array('');
	$htm[] = '		</tbody>';
	$htmOutput = join ("\n", $htm);
	if (gettype ($f) == "resource")
		fwrite ($f, $htmOutput) or die ("Cannot write_table_body"); 
	else
		$long_string .= $htmOutput;
	return $long_string;
}

function write_table_row ($f, $polygon)
// helps:	here write_table_body()
{
	$htm = array('');
	$htm[] = '			<tr>'
.'<td>'.$polygon[feature_id].'<td>'.$polygon[rings].'<td>'.$polygon[polygon_area].'<td>'.$polygon[vri_live_stems_per_ha].'<td>'.$polygon[live_stems]
.'<td>'.$polygon[quad_diam_125].'<td>'.$polygon[basal_area].'<td>'.$polygon[live_volume_125]
.'<td>'.$polygon[map_id].'<td>'.$polygon[bec_zone].'<td>'.$polygon[land_cover]
.'<td>'.$polygon[species_cd_1].'<td>'.$polygon[species_pct_1].'<td>'.$polygon[live_stems_1].'<td>'.$polygon[proj_age_1].'<td>'.$polygon[proj_height_1]
.'<td>'.$polygon[species_cd_2].'<td>'.$polygon[species_pct_2].'<td>'.$polygon[live_stems_2].'<td>'.$polygon[proj_age_2].'<td>'.$polygon[proj_height_2]
.'<td>'.$polygon[species_cd_3].'<td>'.$polygon[species_pct_3].'<td>'.$polygon[live_stems_3]
.'<td>'.$polygon[species_cd_4].'<td>'.$polygon[species_pct_4].'<td>'.$polygon[live_stems_4]
.'<td>'.$polygon[species_cd_5].'<td>'.$polygon[species_pct_5].'<td>'.$polygon[live_stems_5]
.'<td>'.$polygon[species_cd_6].'<td>'.$polygon[species_pct_6].'<td>'.$polygon[live_stems_6]
	;
	$htmOutput = join ("\n", $htm);
	if (gettype ($f) == "resource")
		fwrite ($f, $htmOutput) or die ("Cannot write_table_row"); 
	else
		return $htmOutput;
}

function write_table_summary ($f, $summary)
// helps:	here write_html_table()
{
	$htm = array('');
	$htm[] = '			<tr>'
.'<td>'.$summary[polys].'<td>'.$summary[rings].'<td>'.$summary[area_ha].'<td>'.$summary[density].'<td>'.$summary[live_stems]
.'<td>'.$summary[diam_125].'<td>'.$summary[basal_area].'<td>'.$summary[volume_125]
.'<td>'.$summary[map_id].'<td>'.$summary[bec_zone].'<td>'.$summary[land_cover]
.'<td>'.$summary[species_cd_1].'<td>'.$summary[species_pct_1].'<td>'.$summary[live_stems_1].'<td>'.$summary[age_1].'<td>'.$summary[height_1]
.'<td>'.$summary[species_cd_2].'<td>'.$summary[species_pct_2].'<td>'.$summary[live_stems_2].'<td>'.$summary[age_2].'<td>'.$summary[height_2]
.'<td>'.$summary[species_cd_3].'<td>'.$summary[species_pct_3].'<td>'.$summary[live_stems_3]
.'<td>'.$summary[species_cd_4].'<td>'.$summary[species_pct_4].'<td>'.$summary[live_stems_4]
.'<td>'.$summary[species_cd_5].'<td>'.$summary[species_pct_5].'<td>'.$summary[live_stems_5]
.'<td>'.$summary[species_cd_6].'<td>'.$summary[species_pct_6].'<td>'.$summary[live_stems_6]
	;
	$htmOutput = join ("\n", $htm);
	if (gettype ($f) == "resource")
		fwrite ($f, $htmOutput) or die ("Cannot write_table_foot"); 
	else
		return $htmOutput;
}

function write_html_foot ($f)
// helps:	here write_html_table()
{
	$htm = array('');
	$htm[] = '</body>';
	$htm[] = '</html>';
	$htmOutput = join ("\n", $htm);
	fwrite ($f, $htmOutput) or die ("Cannot write_html_foot"); 
}
