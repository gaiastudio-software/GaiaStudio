// file:	misc.js

function delete_features()
// helps:	map_visualization.js: query_database()
//          region_upload.js: load_region()
//			application.php: <input id="delete-features"
{
	set_all_status_lines ("");
	iframe_document (document.getElementById ('results-frame')).getElementById ("results-table-div").innerHTML = "";
	document.getElementById ("chart-controls").style.display = "none";
	var features = ge.getFeatures();
	while (features.getFirstChild()) {
		features.removeChild (features.getFirstChild());
	}
	reset_progress();
}

function reset_progress()
// helps:	delete_features(), progress_bar()
{
	window.clearInterval (interval_id);
	document.getElementById ('progress-control').value = 0;
	document.getElementById ('progress-control').innerHTML = Space; // for older browsers
}

function query_by_fid (fid)
// helps:	write_age_table.php
{
	document.getElementById ('fid-txt').value = fid;
	query_database ('fid');
	$ ('#mask-iframe, .window').hide();
}

function get_xmlhttp_request()
// helps:	many
{
    var ref = null;
    if (window.XMLHttpRequest) {
        ref = new XMLHttpRequest();
    } else if (window.ActiveXObject) { // IE 6
        ref = new ActiveXObject("MSXML2.XMLHTTP.3.0");
    }
    return ref;
}

function get_fid (it_is_required)
// helps:	map_visualization.js: query_database()
//		 	chart_creation.js: create_chart()
{
	var txtfeatureid = document.getElementById ('fid-txt').value;
	var fidlist = document.getElementById ('fid-sel');
	feature_id = txtfeatureid;
	if (txtfeatureid != '') {
		fidlist.selectedIndex = 0;
	} else if (fidlist.options[fidlist.selectedIndex].innerHTML != '') {
		feature_id = fidlist.options[fidlist.selectedIndex].innerHTML;
	} else if (it_is_required) {
		alert (Missing_Feature_ID);
	}
}

function iframe_document( iframe_element )
//
// Return the document inside the given iframe.
// Example HTML line: <iframe id="the-iframe-id" src="the_document_inside.htm"></iframe>
// Example JS usage:  var ifd = iframe_document( document.getElementById ('the-iframe-id') );
{
	return iframe_element.contentDocument || iframe_element.contentWindow.document;
}

function set_all_status_lines (str)
{
	set_results_lines    (str);
	set_simulation_lines (str);
}

function set_results_lines (str)
{
	document.getElementById ('query-head').  innerHTML = str;
	document.getElementById ('query-note').  innerHTML = '';
	document.getElementById ('profile-head').innerHTML = str;
	document.getElementById ('download-map').innerHTML = '';
	document.getElementById ('view-table').  innerHTML = '';
}

function set_simulation_lines (str)
{
	document.getElementById ('simulation-head').innerHTML = str;
	document.getElementById ('chart-div').   innerHTML = str;
}

function highlight_element (element_id)
{
	var element = document.getElementById (element_id);
	var content = element.innerHTML;
	content = '<mark>' + content + '</mark>';
	element.innerHTML = content;
}

function symbolizing()
// helps:	query_database()
{
	return document.getElementById("3d-symbolizing").checked;
}

function symbol_count_limit()
// helps:	query_database()
{
	return document.getElementById("symbol-limit").value;
}

var rad_per_deg = Math.PI/180;
var deg_per_rad = 180/Math.PI;

function arc_length_to_angle (length, radius)
{
	if (radius <= 0) alert ("Error:\n\n Zero or negative radius passed to arc_length_to_angle()");
	return deg_per_rad * length / radius;
}
