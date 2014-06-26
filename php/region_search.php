<?php
// file:	region_search.php
// helps:	application.php
?>
	<h4>Search by Uploaded Region</h4>
	<label>Browse for KML file<input id="choose-region"   type="file" name="file" accept="application/vnd.google-earth.kml+xml"></label>
	<!-- TODO: also accept , application/vnd.google-earth.kmz -->
	<br>
	<input id="load-region"     type="button" value="Create Query Region"     onclick="load_region();">
	<input id="display-region"  type="button" value="Display Region Features" onclick="query_database ('reg');">
	<input id="delete-region"   type="button" value="Delete Query Region"     onclick="delete_region();">
