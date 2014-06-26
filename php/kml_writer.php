<?php
// file:	kml_writer.php
// helps:	submit_map_query.php

///////////////////// HIGH /////////////////////////

function write_kml_head ($f)
// helps:	submit_map_query.php 
{
	$kml = array();
	$kml[] = XML_Line;
	$kml[] = KML_Line;
	$kml[] = '<Document>';
	$kmlOutput = join ("\n", $kml);
	fwrite ($f, $kmlOutput) or die ("Cannot write kml file head"); 
}

function write_kml_zone_head ($f, $label, $description)
// helps:	submit_map_query.php 
{
	$centroid = centroid_from_polygon (zone_table());
	$range = range_from_polygon (zone_table());
	$kml = array("");
	$kml[] = '	'. name_description_kml (Zone_prefix.$label, labeling_document(), $description, describing_document());
	$kml[] = '	'. lookat_kml ($centroid, $range);
	// maybe insert zone boundary here
	$kmlOutput = join ("\n", $kml);
	fwrite ($f, $kmlOutput) or die ("Cannot write kml name-lookat"); 
}

function write_kml_zone_body ($f, $symbolization_factor)
// helps:	submit_map_query.php 
{
	$polygon_symbol_count = 0; 
	$total_symbol_count = 0;
	$tab = polygons_from_extract();
	while ($polygon = pg_fetch_array ($tab)) {
		$polygon_symbol_count = (int)($polygon[area_ha] * $polygon[vri_live_stems_per_ha] / $symbolization_factor);
		write_polygon_placemark ($f, $polygon, false);
		if (symbolizing() && $polygon_symbol_count) {
			$random_points = simulate_locations ($polygon, $symbolization_factor);
			write_polygon_folder_1 ($f, $polygon, $random_points, $symbolization_factor);
			write_polygon_folder_2 ($f, $polygon, $random_points, $symbolization_factor);
		}
		$total_symbol_count += $polygon_symbol_count;
	}
	return $total_symbol_count;
}

function write_kml_feature_body ($f, $symbolization_factor, $feature_id)
// helps:	submit_map_query.php 
{
	$polygon = extract_feature ($feature_id);
	$polygon_symbol_count = (int)($polygon[area_ha] * $polygon[vri_live_stems_per_ha] / $symbolization_factor); // max potential count
	write_polygon_placemark ($f, $polygon, true);
	if (symbolizing() && $polygon_symbol_count) {
		$random_points = simulate_locations ($polygon, $symbolization_factor);
		write_polygon_folder_1 ($f, $polygon, $random_points, $symbolization_factor);
		write_polygon_folder_2 ($f, $polygon, $random_points, $symbolization_factor);
	}
	return $polygon_symbol_count;
}

function write_kml_foot ($f)
// helps:	submit_map_query.php 
{
	$kml = array("");
	$kml[] = '</Document>';
	$kml[] = '</kml>';
	$kmlOutput = join ("\n", $kml);
	fwrite ($f, $kmlOutput) or die ("Cannot write kml file foot"); 
}

///////////////////// MEDIUM /////////////////////////

function write_polygon_placemark ($f, $polygon, $is_single)
// helps:	here write_kml_zone_body(), write_kml_feature_body() 
{
	$description = $polygon[land_cover].', '.($polygon[live_stems] > 0 ? $polygon[live_stems] : 0).' trees, '.sprintf ("%5.1f", $polygon[area_ha]).' ha'; 
	$kml = array("");
	if ($is_single)
		$kml[] = '	'. lookat_kml (explode (',', $polygon[centroid_str]), $polygon[range]); 
	$kml[] = '	<Placemark>';
	$kml[] = '		'. name_description_kml (Feature_prefix.$polygon[feature_id], labeling_features(), $description, describing_features()); 
	$kml[] = '		'. lookat_kml (explode (',', $polygon[centroid_str]), $polygon[range]);
	$kml[] = '		'. style_kml ($polygon);
	$kml[] = '		'. $polygon[boundary];
	$kml[] = '	</Placemark>';
	$kmlOutput = join ("\n", $kml);
	fwrite ($f, $kmlOutput) or die ("Cannot write polygon placemark"); 
}

function write_polygon_folder_1 ($f, $polygon, $random_points, $symbolization_factor)
// helps:	here write_kml_zone_body(), write_kml_feature_body() 
{
	if ($polygon[live_stems_1] && $polygon[proj_height_1]) {
		$total_symbols_1 = prepare_polygon_symbols_1 ($polygon, $symbolization_factor, $year_arr, $dhgt_arr);
		fwrite ($f, "\n	<Folder>") or die ("Cannot write polygon folder head");
		fwrite ($f, "\n		".name_description_kml ('Species_1, '.$polygon[species_cd_1], labeling_species(), $polygon[live_stems_1].' trees, ('.$total_symbols_1.' symbols)', describing_species())) 
			or die ("Cannot write polygon folder labels"); 
		write_polygon_symbols_1 ($f, $polygon, $random_points, $total_symbols_1, $year_arr, $dhgt_arr);
		fwrite ($f, "\n	</Folder>") or die ("Cannot write polygon folder foot");
	}
}

function write_polygon_folder_2 ($f, $polygon, $random_points, $symbolization_factor)
// helps:	here write_kml_zone_body(), write_kml_feature_body() 
{
	if ($polygon[live_stems_2] && $polygon[proj_height_2]) {
		$total_symbols_2 = prepare_polygon_symbols_2 ($polygon, $symbolization_factor);
		fwrite ($f, "\n	<Folder>") or die ("Cannot write polygon folder head");
		fwrite ($f, "\n		".name_description_kml ('Species_2, '.$polygon[species_cd_2], labeling_species(), $polygon[live_stems_2].' trees, ('.$total_symbols_2.' symbols)', describing_species())) 
			or die ("Cannot write polygon folder labels"); 
		write_polygon_symbols_2 ($f, $polygon, $random_points, $total_symbols_2);
		fwrite ($f, "\n	</Folder>") or die ("Cannot write polygon folder foot");
	}
}

function prepare_polygon_symbols_1 ($polygon, $symbolization_factor, &$year_arr, &$dhgt_arr)
// helps:	here write_polygon_folder_1
{
	$simulations = simulate_growth_yield ($polygon);
	if ($simulations) {
		$year_arr = json_decode ($simulations['year']);
		$dhgt_arr = json_decode ($simulations['dhgt']);
	} else {
		$year_arr = array (Project_Year);
		$dhgt_arr = array ($polygon['proj_height_1']);
	}
	return (int)($polygon[live_stems_1] / $symbolization_factor);
}

function prepare_polygon_symbols_2 ($polygon, $symbolization_factor)
// helps:	here write_polygon_folder_2
{
	return (int)($polygon[live_stems_2] / $symbolization_factor);
}

function write_polygon_symbols_1 ($f, $polygon, $random_points, $total, $year_arr, $dhgt_arr)
// helps:	here write_polygon_folder_1
{
	for ($symbol_count = 1; $symbol_count <= $total; $symbol_count++) 
	{
		$row = pg_fetch_row ($random_points) or die ("No more random_points!");
		$lon_lat = explode (",",$row[0]);
		$steps = count ($year_arr);
		for ($yr_index = 0; $yr_index < $steps; $yr_index += Kino_Factor) // TODO: More intelligent sampling
		{
			$tree_ns = arc_length_to_angle ($dhgt_arr[$yr_index], Earth_Radius);
			$tree_ew = $tree_ns / cos (deg2rad (explode (',', $polygon[centroid_str])[1]));
	// TODO: Spin off write_symbol_placemark_1 ()
			$kml = array ("\n".
							 '		<Placemark>');
			$kml[] = '			<name>species_1, '.$polygon[species_cd_1].',  symbol '.$symbol_count.',  yr_index '.$yr_index.'</name>';
			if ($steps > 1)
				$kml[] = '			<TimeSpan><begin>'. ($year_arr[$yr_index] + 1).'-01</begin><end>'. ($year_arr[$yr_index] + VDYP_Period * Kino_Factor).'-12</end></TimeSpan>';
			$kml[] = '			<Region>';
			$kml[] = '				<Lod> <minLodPixels>'.tree_sym_lod().'</minLodPixels> </Lod>';
			$kml[] = '				<LatLonAltBox>';
			$kml[] = '					<north>'.sprintf (KML_Format, $lon_lat[1] + $tree_ns).'</north> <south>'.sprintf (KML_Format, $lon_lat[1] - $tree_ns).'</south>'; 
			$kml[] = '					<east>'.sprintf (KML_Format, $lon_lat[0] + $tree_ew).'</east>   <west>'.sprintf (KML_Format, $lon_lat[0] - $tree_ew).'</west>'; 
			$kml[] = '				</LatLonAltBox>';
			$kml[] = '			</Region>';
			$kml[] = '			<Model>';
			$kml[] = '				<Location> <longitude>'.$lon_lat[0].'</longitude> <latitude>'.$lon_lat[1].'</latitude> </Location>';
//			$kml[] = '				<Orientation> <heading>0</heading> <tilt>0</tilt> <roll>0</roll> </Orientation>';
			$kml[] = '				<Scale> <x>'.$dhgt_arr[$yr_index] / model_height().'</x> <y>'.$dhgt_arr[$yr_index] / model_height().'</y> <z>'.$dhgt_arr[$yr_index] / model_height().'</z> </Scale>';
			$kml[] = '				<Link><href>'.Models_Href.'/pseudotsuga_menziesii.dae</href></Link>';
//			$kml[] = '				<Link><href>'.Models_Href.'/thuja_plicata.dae</href></Link>';
			$kml[] = '			</Model>';
			$kml[] = '		</Placemark>';
			$kmlOutput = join ("\n", $kml);
			fwrite ($f, $kmlOutput) or die ("Cannot write kml symbol_1");
		}
	}
}

function write_polygon_symbols_2 ($f, $polygon, $random_points, $total)
// helps:	here write_polygon_folder_2
{
	$tree_ns = arc_length_to_angle ($polygon[proj_height_2], Earth_Radius);
	$tree_ew = $tree_ns / cos (deg2rad (explode (',', $polygon[centroid_str])[1]));
	for ($symbol_count = 1; $symbol_count <= $total; $symbol_count++) 
	{
		// TODO: Spin off write_symbol_placemark_2 ()
		$row = pg_fetch_row ($random_points) or die ("No more random_points!");
		$lon_lat = explode (",",$row[0]);
		$kml = array ("\n".
						 '		<Placemark>');
		$kml[] = '			<name>species_2, '.$polygon[species_cd_2].',  symbol '.$symbol_count.'</name>';
		$kml[] = '			<Region>';
		$kml[] = '				<Lod> <minLodPixels>'.tree_sym_lod().'</minLodPixels> </Lod>';
		$kml[] = '				<LatLonAltBox>';
		$kml[] = '					<north>'.sprintf (KML_Format, $lon_lat[1] + $tree_ns).'</north> <south>'.sprintf (KML_Format, $lon_lat[1] - $tree_ns).'</south>'; 
		$kml[] = '					<east>'.sprintf (KML_Format, $lon_lat[0] + $tree_ew).'</east>   <west>'.sprintf (KML_Format, $lon_lat[0] - $tree_ew).'</west>'; 
		$kml[] = '				</LatLonAltBox>';
		$kml[] = '			</Region>';
		$kml[] = '			<Model>';
		$kml[] = '				<Location> <longitude>'.$lon_lat[0].'</longitude> <latitude>'.$lon_lat[1].'</latitude> </Location>';
//		$kml[] = '				<Orientation> <heading>0</heading> <tilt>0</tilt> <roll>0</roll> </Orientation>';
		$kml[] = '				<Scale> <x>'.$polygon[proj_height_2] / model_height().'</x> <y>'.$polygon[proj_height_2] / model_height().'</y> <z>'.$polygon[proj_height_2] / model_height().'</z> </Scale>';
		$kml[] = '				<Link><href>'.Models_Href.'/abies_amabilis.dae</href></Link>';
		$kml[] = '			</Model>';
		$kml[] = '		</Placemark>';
		$kmlOutput = join ("\n", $kml);
		fwrite ($f, $kmlOutput) or die ("Cannot write kml symbol_2");
	}
}

///////////////////// GENERAL /////////////////////////

function style_kml ($polygon) 
// helps:	here write_polygon_placemark()
{
	switch (substr ($polygon[land_cover], 0, 1))	{
	case "N":
	case "T":
		$fill_BGR = stand_fill_BGR();
		$line_BGR = stand_line_BGR();
		break;
	case "W":
		$fill_BGR = water_fill_BGR();
		$line_BGR = water_line_BGR();
		break;
	case "L":
		$fill_BGR = bare_fill_BGR();
		$line_BGR = bare_line_BGR();
		break;
	default:
		$fill_BGR = unrep_fill_BGR();
		$line_BGR = unrep_line_BGR();
	}
	return '<Style> <PolyStyle><color>'.fill_opacity().$fill_BGR.'</color></PolyStyle>	<LineStyle><color>'.line_opacity().$line_BGR.'</color><width>'.line_width().'</width></LineStyle> </Style>';
}

function name_description_kml ($name, $naming, $description, $describing) 
// helps:	here write_kml_zone_head(), write_polygon_placemark(), 
//	           write_polygon_folder_1(), write_polygon_folder_2()
{
	$name_kml = strlen ($name) 
		? ($naming ? '' : '<!-- ').'<name>'.$name.'</name>'.($naming ? '' : ' -->')
		: "";
	$description_kml = strlen ($description) 
		? ($describing ? '' : '<!-- ').'<description>'.$description.'</description>'.($describing ? '' : ' -->')
		: "";
	return $name_kml.$description_kml;
// TODO: snippet
}

function lookat_kml ($centroid, $range)
// helps:	here write_kml_zone_head(), write_polygon_placemark() 
{
	return '<LookAt> <longitude>'.$centroid[0].'</longitude> <latitude>'.$centroid[1].'</latitude> <range>'
		.$range.'</range> <tilt>'.look_tilt().'</tilt> <heading>'.look_heading().'</heading> </LookAt>';
}
