<?php
// file:	queries.php
// helps:	submit_map_query.php, kml_writer.php, html_writer.php

function intersect_zone() 
// helps:	submit_map_query.php extract_zone_from_main()
{
	$build_section_sql = "
		SELECT feature_id, map_id
		, ST_Intersection (".Main_Table.".geom, ".zone_table().".geom) AS geom
		, ST_Area (ST_Intersection (".Main_Table.".geom, ".zone_table().".geom)) / 10000  AS new_area_ha
		, concat (bclcs_level_2,'.', bclcs_level_3,'.', bclcs_level_4,'.', bclcs_level_5) AS land_cover
		, concat (bec_zone_code,'.', bec_subzone) AS bec_zone
		, vri_live_stems_per_ha 
		, quad_diam_125, basal_area, live_stand_volume_125 AS live_volume_125
		, species_cd_1, species_pct_1, proj_age_1, proj_height_1
		, species_cd_2, species_pct_2, proj_age_2, proj_height_2
		, species_cd_3, species_pct_3
		, species_cd_4, species_pct_4
		, species_cd_5, species_pct_5
		, species_cd_6, species_pct_6
		, (proj_height_1 / ".model_height().")::real AS scale_1
		, (proj_height_2 / ".model_height().")::real AS scale_2
		INTO ".sect_table()." FROM ".Main_Table.", ".zone_table()."
		WHERE ST_Intersects (".Main_Table.".geom, ".zone_table().".geom)
		;";
	pg_query (vri_connection(), $build_section_sql) or die ("build_section_sql failed");
	
	$count_section_sql = "SELECT count(*) FROM ".sect_table();
	$tab = pg_query (vri_connection(), $count_section_sql) or die ("count_section_sql failed");
	$arr = pg_fetch_array ($tab) or die ("pg_fetch_array() failed in intersect_zone()");
	return $arr[count];
}

function extract_sect()
// helps:	submit_map_query.php extract_zone_from_main()
{
	$build_extract_sql = "
		SELECT ".sect_table().".feature_id, map_id, geom
		, new_area_ha                        AS area_ha
		, ST_Xmax (geom) - ST_Xmin (geom)    AS dx
		, ST_Ymax (geom) - ST_Ymin (geom)    AS dy
		, ST_AsKML (geom, ".KML_Precision.") AS boundary
		, ST_NRings (geom)                   AS rings 
		, land_cover, bec_zone, vri_live_stems_per_ha
		, (new_area_ha * vri_live_stems_per_ha)::integer AS live_stems
		, quad_diam_125, basal_area, live_volume_125
		, species_cd_1, species_pct_1, proj_age_1, proj_height_1
		, species_cd_2, species_pct_2, proj_age_2, proj_height_2
		, species_cd_3, species_pct_3
		, species_cd_4, species_pct_4
		, species_cd_5, species_pct_5
		, species_cd_6, species_pct_6
		, (new_area_ha * vri_live_stems_per_ha * species_pct_1 / 100)::integer AS live_stems_1
		, (new_area_ha * vri_live_stems_per_ha * species_pct_2 / 100)::integer AS live_stems_2
		, (new_area_ha * vri_live_stems_per_ha * species_pct_3 / 100)::integer AS live_stems_3
		, (new_area_ha * vri_live_stems_per_ha * species_pct_4 / 100)::integer AS live_stems_4
		, (new_area_ha * vri_live_stems_per_ha * species_pct_5 / 100)::integer AS live_stems_5
		, (new_area_ha * vri_live_stems_per_ha * species_pct_6 / 100)::integer AS live_stems_6
		, scale_1, scale_2
		
		, CASE WHEN quad_diam_125 > 0 THEN (new_area_ha * vri_live_stems_per_ha)::integer ELSE 0 END AS d_125_stems
		, new_area_ha * vri_live_stems_per_ha * quad_diam_125                                        AS stems_diam_125
		, CASE WHEN basal_area      > 0 THEN new_area_ha ELSE 0 END AS ba_area
		, new_area_ha * basal_area                                  AS area_basal_area
		, CASE WHEN live_volume_125 > 0 THEN new_area_ha ELSE 0 END AS v_125_area
		, new_area_ha * live_volume_125                             AS area_volume_125
		, new_area_ha * vri_live_stems_per_ha * species_pct_1 / 100 * proj_age_1    AS stems_age_1
		, new_area_ha * vri_live_stems_per_ha * species_pct_2 / 100 * proj_age_2    AS stems_age_2
		, new_area_ha * vri_live_stems_per_ha * species_pct_1 / 100 * proj_height_1 AS stems_height_1
		, new_area_ha * vri_live_stems_per_ha * species_pct_2 / 100 * proj_height_2 AS stems_height_2
		
		, scale_values (new_area_ha * vri_live_stems_per_ha, age::real[]) AS stems_age
		, scale_values (new_area_ha * vri_live_stems_per_ha, dhgt)        AS stems_dhgt
		, scale_values (new_area_ha * vri_live_stems_per_ha, lhgt)        AS stems_lhgt
		, scale_values (new_area_ha * vri_live_stems_per_ha, dia)         AS stems_dia
		, scale_values (new_area_ha, ba)   AS area_ba
		, scale_values (new_area_ha, tph)  AS area_tph
		, scale_values (new_area_ha, vws)  AS area_vws
		, scale_values (new_area_ha, vcu)  AS area_vcu
		, scale_values (new_area_ha, vd)   AS area_vd
		, scale_values (new_area_ha, vdw)  AS area_vdw
		, scale_values (new_area_ha, vdwb) AS area_vdwb
		INTO ".extr_table()." FROM ".sect_table()." LEFT JOIN ".Yield_Table."
		ON ".sect_table().".feature_id = ".Yield_Table.".feature_id
		;";
	pg_query (vri_connection(), $build_extract_sql) or die ("build_extract_sql failed");
}

function extract_feature ($feature_id)
// helps:	kml_writer.php write_kml_feature_body()
{
	$get_feature_sql = "SELECT feature_id, geom, polygon_area AS area_ha
		, ST_asEWKT (geom) AS geom_ewkt
		, btrim (ST_AsKML (ST_Centroid (geom), ".(KML_Precision - 2)."), 'Point</>coordinates') AS centroid_str
		, (".pol_range_factor()." * GREATEST (ST_Xmax (geom) - ST_Xmin (geom), ST_Ymax (geom) - ST_Ymin (geom)))::integer AS range
		, ST_AsKML (geom, ".KML_Precision.") AS boundary
		, concat(bclcs_level_2,'.', bclcs_level_3,'.', bclcs_level_4,'.', bclcs_level_5) AS land_cover
		, concat (bec_zone_code,'.', bec_subzone) AS bec_zone
		, vri_live_stems_per_ha 
		, (polygon_area * vri_live_stems_per_ha)::integer AS live_stems
		, quad_diam_125, basal_area, live_stand_volume_125
		, species_cd_1, species_pct_1, proj_age_1, proj_height_1
		, species_cd_2, species_pct_2, proj_age_2, proj_height_2
		, species_cd_3, species_pct_3
		, species_cd_4, species_pct_4
		, species_cd_5, species_pct_5
		, species_cd_6, species_pct_6
		,	(polygon_area * vri_live_stems_per_ha * species_pct_1 / 100)::integer AS live_stems_1
		,	(polygon_area * vri_live_stems_per_ha * species_pct_2 / 100)::integer AS live_stems_2
		,	(polygon_area * vri_live_stems_per_ha * species_pct_3 / 100)::integer AS live_stems_3
		,	(polygon_area * vri_live_stems_per_ha * species_pct_4 / 100)::integer AS live_stems_4
		, (polygon_area * vri_live_stems_per_ha * species_pct_5 / 100)::integer AS live_stems_5
		, (polygon_area * vri_live_stems_per_ha * species_pct_6 / 100)::integer AS live_stems_6
		, (proj_height_1 / ".model_height().")::real AS scale_1
		, (proj_height_2 / ".model_height().")::real AS scale_2
		FROM ".Main_Table."
		WHERE feature_id = ".$feature_id.";";
	$tab = pg_query (vri_connection(), $get_feature_sql) or die ("get_feature_sql failed");
	$arr = pg_fetch_array ($tab) or die ("pg_fetch_array() failed in extract_feature()");
	return $arr;
}

function polygons_from_extract()
// helps:	kml_writer.php write_kml_zone_body()
{
	$get_polygons_sql = "SELECT feature_id, geom, area_ha
		, ST_asEWKT (geom) AS geom_ewkt
		, btrim (ST_AsKML (ST_Centroid (geom), ".(KML_Precision - 2)."), 'Point</>coordinates') AS centroid_str
		, (".pol_range_factor()." * GREATEST (dx, dy))::integer AS range
		, boundary
		, land_cover, bec_zone, vri_live_stems_per_ha
		, quad_diam_125, basal_area, live_volume_125
		, species_cd_1, species_pct_1, proj_age_1, proj_height_1
		, species_cd_2, species_pct_2, proj_age_2, proj_height_2
		, species_cd_3, species_pct_3
		, species_cd_4, species_pct_4
		, species_cd_5, species_pct_5
		, species_cd_6, species_pct_6
		, live_stems
		, live_stems_1, live_stems_2, live_stems_3, live_stems_4, live_stems_5, live_stems_6
		, scale_1, scale_2
		FROM ".extr_table().";";
	$tab = pg_query (vri_connection(), $get_polygons_sql) or die ("get_polygons_sql failed");
	return $tab;
}

function summarize_extract()
// helps:	submit_map_query.php extract_zone_from_main()
{
	$summarize_polys_sql = "
		SELECT count (*)     AS polys
		, sum (rings)        AS rings 
		, to_char (sum (area_ha), '999999D9')                 AS area_ha 
		, sum (live_stems)                                    AS live_stems 
		, to_char (sum (live_stems) / sum (area_ha), '99999') AS density 
		, sum (live_stems_1) AS live_stems_1 
		, sum (live_stems_2) AS live_stems_2 
		, sum (live_stems_3) AS live_stems_3 
		, sum (live_stems_4) AS live_stems_4 
		, sum (live_stems_5) AS live_stems_5 
		, sum (live_stems_6) AS live_stems_6 
		, to_char (sum (stems_diam_125) / sum (d_125_stems), '999D9')  AS diam_125 
		, to_char (sum (area_basal_area) / sum (ba_area),    '999D9')  AS basal_area 
		, to_char (sum (area_volume_125) / sum (v_125_area), '9999D9')  AS volume_125 
		, to_char (sum (stems_age_1) / sum (live_stems_1),   '9999')  AS age_1 
		, to_char (sum (stems_age_2) / sum (live_stems_2),   '9999')  AS age_2 
		, to_char (sum (stems_height_1) / sum (live_stems_1), '99D9') AS height_1 
		, to_char (sum (stems_height_2) / sum (live_stems_2), '99D9') AS height_2 
		FROM ".extr_table().";";
	$tab = pg_query (vri_connection(), $summarize_polys_sql) or die ("summarize_polys_sql failed");
	$arr = pg_fetch_array ($tab) or die ("pg_fetch_array() failed in summarize_extract()");
	return $arr;
}

function summarize_feature ($feature_id) 
// helps:	submit_map_query.php ()
{
	$summarize_feature_sql = "
		SELECT 1           AS polys
		, ST_NRings (geom) AS rings
		, to_char (polygon_area, '999999D9')              AS area_ha 
		,	(polygon_area * vri_live_stems_per_ha)::integer AS live_stems
		, vri_live_stems_per_ha                           AS density
		,	(polygon_area * vri_live_stems_per_ha * species_pct_1 / 100)::integer AS live_stems_1
		,	(polygon_area * vri_live_stems_per_ha * species_pct_2 / 100)::integer AS live_stems_2
		,	(polygon_area * vri_live_stems_per_ha * species_pct_3 / 100)::integer AS live_stems_3
		,	(polygon_area * vri_live_stems_per_ha * species_pct_4 / 100)::integer AS live_stems_4
		,	(polygon_area * vri_live_stems_per_ha * species_pct_5 / 100)::integer AS live_stems_5
		,	(polygon_area * vri_live_stems_per_ha * species_pct_6 / 100)::integer AS live_stems_6
		, to_char (quad_diam_125, '999D9')           AS diam_125 
		, to_char (basal_area, '999D9')              AS basal_area 
		, to_char (live_stand_volume_125, '99999D9') AS volume_125 
		, proj_age_1  AS age_1 
		, proj_age_2  AS age_2 
		, to_char (proj_height_1, '99D9') AS height_1 
		, to_char (proj_height_2, '99D9') AS height_2 
		FROM ".Main_Table."
		WHERE feature_id = ".$feature_id.";";
	$tab = pg_query (vri_connection(), $summarize_feature_sql) or die ("summarize_feature_sql failed");
	$arr = pg_fetch_array ($tab); // or die ("No feature $feature_id found.");
	return $arr;
}

function condense_extract()
// helps:	submit_map_query.php extract_zone_from_main()
{
	$condense_polys_sql = "
		SELECT map_id, land_cover, bec_zone, species_cd_1, species_cd_2, species_cd_3, species_cd_4
		FROM 
		(	SELECT map_id FROM
			(	SELECT map_id
				FROM ".extr_table()."
				GROUP BY map_id
				ORDER BY sum (area_ha) DESC NULLS LAST
			) AS histo
			LIMIT 1
		) AS map_id
		,
		(	SELECT land_cover FROM
			(	SELECT land_cover
				FROM ".extr_table()."
				GROUP BY land_cover
				ORDER BY sum (area_ha) DESC NULLS LAST
			) AS histo
			LIMIT 1
		) AS land_cover
		,
		(	SELECT bec_zone FROM
			(	SELECT bec_zone
				FROM ".extr_table()."
				GROUP BY bec_zone
				ORDER BY sum (area_ha) DESC NULLS LAST
			) AS histo
			LIMIT 1
		) AS bec_zone
		,
		(	SELECT species_cd_1 FROM
			(	SELECT species_cd_1
				FROM ".extr_table()."
				GROUP BY species_cd_1
				ORDER BY sum (live_stems_1) DESC NULLS LAST
			) AS histo
			LIMIT 1
		) AS species_cd_1
		,
		(	SELECT species_cd_2 FROM
			(	SELECT species_cd_2
				FROM ".extr_table()."
				GROUP BY species_cd_2
				ORDER BY sum (live_stems_2) DESC NULLS LAST
			) AS histo
			LIMIT 1
		) AS species_cd_2
		,
		(	SELECT species_cd_3 FROM
			(	SELECT species_cd_3
				FROM ".extr_table()."
				GROUP BY species_cd_3
				ORDER BY sum (live_stems_3) DESC NULLS LAST
			) AS histo
			LIMIT 1
		) AS species_cd_3
		,
		(	SELECT species_cd_4 FROM
			(	SELECT species_cd_4
				FROM ".extr_table()."
				GROUP BY species_cd_4
				ORDER BY sum (live_stems_4) DESC NULLS LAST
			) AS histo
			LIMIT 1
		) AS species_cd_4
		;";
	$tab = pg_query (vri_connection(), $condense_polys_sql) or die ("condense_polys_sql failed");
	$arr = pg_fetch_array ($tab) or die ("pg_fetch_array() failed in condense_extract()");
	return $arr;
}

function zone_statistics()
// helps:	html_writer.php write_table_body()
{
	$statistics_sql = "SELECT feature_id, to_char (area_ha, '99999D9') AS polygon_area, map_id
		, rings, land_cover, bec_zone, vri_live_stems_per_ha, live_stems
		, to_char (quad_diam_125,    '999D9') AS quad_diam_125
		, to_char (basal_area,       '999D9') AS basal_area
		, to_char (live_volume_125,'99999D9') AS live_volume_125
		, species_cd_1, species_pct_1, live_stems_1, proj_age_1, to_char (proj_height_1, '999D9') AS proj_height_1
		, species_cd_2, species_pct_2, live_stems_2, proj_age_2, to_char (proj_height_2, '999D9') AS proj_height_2
		, species_cd_3, species_pct_3, live_stems_3
		, species_cd_4, species_pct_4, live_stems_4
		, species_cd_5, species_pct_5, live_stems_5
		, species_cd_6, species_pct_6, live_stems_6
		FROM ".extr_table()." ORDER BY area_ha DESC;"; 
	$tab = pg_query (vri_connection(), $statistics_sql) or die ("statistics_sql failed");
	return $tab;
}

function feature_statistics ($feature_id) 
// helps:	html_writer.php write_table_body()
{
	$statistics_sql = "SELECT feature_id, to_char (polygon_area, '99999D9') AS polygon_area, map_id
		, ST_NRings (geom) AS rings
		, concat(bclcs_level_2,'.', bclcs_level_3,'.', bclcs_level_4,'.', bclcs_level_5) AS land_cover
		, concat (bec_zone_code,'.', bec_subzone) AS bec_zone
		, vri_live_stems_per_ha
		, (vri_live_stems_per_ha * polygon_area)::integer AS live_stems
		, to_char (quad_diam_125,          '999D9') AS quad_diam_125
		, to_char (basal_area,             '999D9') AS basal_area
		, to_char (live_stand_volume_125,'99999D9') AS live_volume_125
		,	(polygon_area * vri_live_stems_per_ha * species_pct_1 / 100)::integer AS live_stems_1
		,	(polygon_area * vri_live_stems_per_ha * species_pct_2 / 100)::integer AS live_stems_2
		,	(polygon_area * vri_live_stems_per_ha * species_pct_3 / 100)::integer AS live_stems_3
		,	(polygon_area * vri_live_stems_per_ha * species_pct_4 / 100)::integer AS live_stems_4
		,	(polygon_area * vri_live_stems_per_ha * species_pct_5 / 100)::integer AS live_stems_5
		,	(polygon_area * vri_live_stems_per_ha * species_pct_6 / 100)::integer AS live_stems_6
		, species_cd_1, species_pct_1, proj_age_1, to_char (proj_height_1, '999D9') AS proj_height_1
		, species_cd_2, species_pct_2, proj_age_2, to_char (proj_height_2, '999D9') AS proj_height_2
		, species_cd_3, species_pct_3
		, species_cd_4, species_pct_4
		, species_cd_5, species_pct_5
		, species_cd_6, species_pct_6
		FROM ".Main_Table."
		WHERE feature_id = ".$feature_id.";";
	$tab = pg_query (vri_connection(), $statistics_sql) or die ("statistics_sql failed in feature_statistics()");
	return $tab;
}

///////////////////// MEDIUM /////////////////////////

function simulate_growth_yield ($polygon)
// helps:	kml_writer.php prepare_polygon_symbols_1()
{
	$get_simulations_sql = "SELECT array_to_json (dhgt[".Yr_index_beg.": ".Yr_index_end."]) AS dhgt, array_to_json (year[".Yr_index_beg.": ".Yr_index_end."]) AS year 
		FROM ".Yield_Table." WHERE feature_id = $polygon[feature_id];";
	$tab = pg_query (vri_connection(), $get_simulations_sql) or die ("get_simulations_sql query failed");
	$arr = pg_fetch_array ($tab);
	return $arr;
}

function simulate_locations ($polygon, $symbolization_factor)
// helps:	kml_writer.php write_kml_zone_body(), write_kml_feature_body()
{
	$random_points_sql = "
		SELECT btrim (ST_AsKML (RandomPointsInPolygon 
			(ST_geomFromText ('".$polygon[geom_ewkt]."'), (".$polygon[area_ha]." * ".$polygon[vri_live_stems_per_ha]." / ".$symbolization_factor.")::integer + 1), 
		".KML_Precision."), 'Point</>coordinates');"; // + 1 prevents round-up error
	$tab = pg_query (vri_connection(), $random_points_sql) or die ("random_points_sql failed");
	return $tab; 
}

///////////////////// GENERAL /////////////////////////

function make_map_polygon_from_kml_polygon ($polygon_tab, $polygon_kml, $map_srid) 
// helps:	submit_map_query.php extract_zone_from_main()
{
	$build_sql = "SELECT ST_Transform (ST_GeomFromKML ('$polygon_kml'), $map_srid) AS geom INTO $polygon_tab;";
	pg_query (vri_connection(), $build_sql) or die ("pg_query() failed in make_map_polygon_from_kml_polygon()");
}

function centroid_from_polygon ($polygon_tab) 
// helps:	kml_writer.php write_kml_zone_head()
{
	$precision = KML_Precision - 2;
	$get_centroid_sql = "SELECT btrim (ST_AsKML (ST_Centroid (geom), $precision), 'Point</>coordinates') AS centroid FROM $polygon_tab;";
	$tab = pg_query (vri_connection(), $get_centroid_sql) or die ("get_centroid_sql failed");
	$row = pg_fetch_row ($tab) or die ("pg_fetch_row() failed in centroid_from_polygon()");
	return explode (',', $row[0]);
}

function range_from_polygon ($polygon_tab) 
// helps:	kml_writer.php write_kml_zone_head()
{
	$get_size_sql = "SELECT ST_Xmax (geom) - ST_Xmin (geom) AS dx, ST_Ymax (geom) - ST_Ymin (geom) AS dy FROM $polygon_tab;";
	$tab = pg_query (vri_connection(), $get_size_sql) or die ("get_size_sql failed");
	$row = pg_fetch_row ($tab) or die ("pg_fetch_row() failed in range_from_polygon()");
	return (int)(doc_range_factor() * max($row));
}

function area_from_polygon ($polygon_tab) 
// helps:	
{
	$area_sql =	"SELECT ST_Area (geom) / 10000 AS area FROM $polygon_tab;"; 
	$tab = pg_query (vri_connection(), $area_sql) or die ("area_sql failed");
	$row = pg_fetch_row ($tab) or die ("pg_fetch_row() failed in area_from_polygon()");
	return $row[0];
}

function drop_table ($tab) 
// helps:	submit_map_query.php drop_temp_tables()
{
	$drop_sql = "DROP TABLE IF EXISTS $tab;";
	pg_query (vri_connection(), $drop_sql) or die ("pg_query() failed in drop_table()");
}
