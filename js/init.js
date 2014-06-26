// file:	init.js

var project_year;
var PHP_Href;
var Images_Href;
var Busy_Icon;
var ge;
var feature_id = '0';
var org_units_kml;
var initial_long;
var initial_lat;
var initial_range;

google.load ("earth", GE_Version);
google.load ("maps",  GM_Version);

function init_app (proj_yr, php_href, img_href, org_units, ini_long, ini_lat, ini_range)
// helps:	application.php: <body onload
{
	project_year  = proj_yr;
	PHP_Href      = php_href;
	Images_Href   = img_href;
	Busy_Icon     = '<img src="' + Images_Href + '/ajax-loader.gif">';
	org_units_kml = org_units;
	initial_long  = parseFloat (ini_long);
	initial_lat   = parseFloat (ini_lat);
	initial_range = parseFloat (ini_range);
	set_all_status_lines (Busy_Icon + Space + Loading_Google_Earth)
	if (google.earth.isSupported()) {
		google.earth.createInstance ('map-div', init_callback, failure_callback);
		var txtfeatureid = document.getElementById ('fid-txt').value;
		if (txtfeatureid != '') {
			query_database ('none', 'fid');
		}
	} else {
		set_all_status_lines ('The Google Earth plugin is not supported by this browser/platform.');
	}
}

function init_callback (plugin_instance)
// helps:	here: init()
{
	ge = plugin_instance;
	ge.getWindow().setVisibility (            true);
	ge.getSun().setVisibility (               enable_sun);
	ge.getOptions().setScaleLegendVisibility (enable_scale_bar);
	ge.getOptions().setUnitsFeetMiles (       enable_imperial);
	ge.getOptions().setTerrainExaggeration (  terrain_exaggeration);
	ge.getOptions().setFadeInOutEnabled(      enable_fade_inout);
	ge.getOptions().setStatusBarVisibility (  enable_status_bar);
	ge.getOptions().setAtmosphereVisibility ( enable_atmosphere);
	ge.getOptions().setOverviewMapVisibility (enable_overview);
	ge.getOptions().setGridVisibility (       enable_graticule);
//	ge.getTime().setHistoricalImageryEnabled (enable_historical); // This seems to have no effect!
	ge.getNavigationControl().setVisibility (ge.VISIBILITY_AUTO); // MAGIC
	ge.getLayerRoot().enableLayerById (ge.LAYER_TERRAIN,   enable_terrain);
	ge.getLayerRoot().enableLayerById (ge.LAYER_BORDERS,   enable_borders);
	ge.getLayerRoot().enableLayerById (ge.LAYER_ROADS,     enable_roads);
	ge.getLayerRoot().enableLayerById (ge.LAYER_BUILDINGS, enable_buildings);
	ge.getLayerRoot().enableLayerById (ge.LAYER_BUILDINGS_LOW_RESOLUTION, enable_buildings);
	var la = ge.getView().copyAsLookAt (ge.ALTITUDE_RELATIVE_TO_GROUND);
	ge.getView().setAbstractView (la);

//  insert outlines and initialize camera view
	var link = ge.createLink('');
	link.setHref (org_units_kml);
	var networkLink = ge.createNetworkLink('');
	networkLink.set (link, true, false);
	ge.getFeatures().appendChild (networkLink);
	var lookAt = ge.createLookAt('');
	lookAt.setLongitude (initial_long);
	lookAt.setLatitude (initial_lat);
	lookAt.setRange (initial_range);
	ge.getView().setAbstractView (lookAt);
//	end of insert outlines

	document.getElementById ('plugin-version').innerHTML = 'Google Earth Plugin ' + ge.getPluginVersion().toString();
	set_all_status_lines ('');
}

function failure_callback (error_code)
// helps:	here: init()
{
	if        (error_code == "ERR_CREATE_PLUGIN") {
        alert("The Google Earth Plugin is not installed\n\n"
        + "-- or is installed but not supported by this browser.");
    } else if (error_code == "SUCCESS_RECENT_INSTALL_RESTART") {
        alert("The Google Earth Plugin is not supported by IE 10.\n\n"
        + "Try using one of these alternatives:\n"
        + "  o  IE 9.\n"
        + "  o  'Compatibility mode' -- it's that torn paper icon in the address bar.\n"
        + "  o  A better browser: Chrome or Firefox."); //  or Opera
	} else {
		alert ('Error loading the Google Earth Plugin: ' + error_code);
	}
}

