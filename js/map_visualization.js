// file:	map_visualization.js

var interval_id;

function query_database (query_type)
// helps:	application.php: <input id="display-features"
//			application.php: <input id="disp-feature"
// 			misc.js: init(), query_by_fid()
{
	delete_features();
	switch (query_type) {
	case "fid":
		get_fid (true);
		if (feature_id == '') return;
		individual = true ;
		break;
	case "box":
		if (! box_is_created()) {
			alert (Missing_Query_Box);
			return;
		}
		individual = false ;
		break;
	case "reg":
		if (! document.getElementById ("choose-region").value) {
			alert (Missing_Query_File);
			return;
		}
		if (! region_is_created()) {
			alert (Missing_Query_Region);
			return;
		}
		individual = false ;
	case "map":
		individual = false ;
	}
	xmlhttp = get_xmlhttp_request();
	var params = "query_type=" + query_type + "&" +
		(query_type == 'box' ? "zone=" +    the_zone :
	     query_type == 'reg' ? "zone=" +    the_zone :
	     query_type == 'fid' ? "feature=" + feature_id :
	     query_type == 'map' ? "map_id=" +  map_id :
	                           "error=error") + "&" +
	             "user_region=" + user_region + "&" +
	             "symbolizing=" + symbolizing() + "&" +
	             "symbol_limit=" + symbol_count_limit();
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == RS_4_COMPLETE && xmlhttp.status == SC_200_OK) {
			var resp = xmlhttp.responseText;
			var resp_arr = resp.split ("%%%");
//			alert(resp_arr.join('\n\n'));// DEBUG // KEEP
			if (resp_arr[0] == "OK") {
				document.getElementById ('query-head').innerHTML = resp_arr[1];
				document.getElementById ('query-note').innerHTML = resp_arr[2];
				if (resp_arr[3] == "") {
					set_simulation_lines ('');
					document.getElementById ('profile-head').innerHTML = '';
					document.getElementById ('view-table').innerHTML = '';
					ge.getWindow().setVisibility (true);
				} else {
					load_feature (resp_arr[3]);
					show_stats   (resp_arr[4], resp_arr[5], resp_arr[6], resp_arr[7]);
				}
			} else {
				alert (resp_arr[0]);
				set_all_status_lines ('');
			}
			document.getElementById ("progress-note").innerHTML = Space;
		} else {
			document.getElementById ('fid-txt').value = individual ? feature_id : '';
			document.getElementById ("progress-note").innerHTML = Space + Searching_Database + Space + Please_Wait + Space;
			set_all_status_lines (Busy_Icon + Space + Searching_Database + Space + Please_Wait + Space);
			ge.getWindow().setVisibility (false);
		}
	}
	xmlhttp.open ("POST",  PHP_Href + "/submit_map_query.php" + "?" + params, true);
	xmlhttp.send();
}

function load_feature (kml_href)
// helps:	query()
//			here: query_database()
{
	create_chart();
	var link = ge.createLink ('');
	link.setHref (kml_href);
	var networkLink = ge.createNetworkLink ('');
	networkLink.set (link, true, true);
    interval_id = window.setInterval (progress_bar, progress_interval_ms);
	ge.getFeatures().appendChild (networkLink);
}

var so;

function progress_bar()
{
	var sp = ge.getStreamingPercent();
	var ph = document.getElementById ("progress-note");
	var pc = document.getElementById ('progress-control');
	pc.value = sp;
	pc.innerHTML = sp + '%'; // for older browsers
	if (sp < 100) {
		ph.innerHTML = Space + Loading_Visualization + Space + Please_Wait + Space + Meantime + Space;
		highlight_element ("progress-note");
	} else {
		ge.getWindow().setVisibility (true);
		ph.innerHTML = Ready;
		reset_progress();
	}
}

function show_stats (polys, str, table_url, map_url)
// helps:	query()
{
	document.getElementById ("profile-head").innerHTML = 'Profile (in ' + project_year + ')';
	dme = document.getElementById ("download-map");
	vte = document.getElementById ("view-table");
	if (parseInt (polys) > 1)
	{
		vte.style.visibility = "visible";
		rows = 2 * Fix_Results_Rows + 1 + parseInt (polys);
		if (rows > Max_Results_Rows) rows = Max_Results_Rows;
	} else {
		vte.style.visibility = "hidden";
		rows = Fix_Results_Rows + 1;
	}
	dme.innerHTML = '<a target="_blank" href="' + map_url + '"><input type="button" value="Download KML File..." title="Download visualization as a KML file"></a>';
	vte.innerHTML = '<a target="_blank" href="' + table_url + '"><input type="button" value="View Full Table..." title="View whole table in separate browser tab"></a>';
	document.getElementById ('results-frame').style.height = rows * Ems_Per_Row + "em";
	iframe_document( document.getElementById ('results-frame') ).getElementById ("results-table-div").innerHTML = str;
}
