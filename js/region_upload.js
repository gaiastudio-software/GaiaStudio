// file:	region_upload.js

var user_region = "";

function load_region()
// helps:	application.php <input id="load-region"
{
	if (! document.getElementById ("choose-region").value) {
		alert (Missing_Query_File);
		return;
	}
	if (window.File && window.FileReader) {
		var file = document.getElementById ("choose-region").files[0];
		file_name = file.name;
		user_region = file.name.substr(0, file.name.lastIndexOf('.kml')) || file.name;
//		alert("Name: " + file.name);//DEBUG
		var reader = new FileReader();
		reader.onload = function(e)
		{
			delete_features();
			var file_string = reader.result;
			add_region (file_string);
			var xml_doc = xml_doc_from_file (file_string);
			xml_doc_to_zone (xml_doc);
		}
		reader.readAsText (file);
	} else {
	  alert('The File API is not supported in this browser.');
	}
}

function delete_region()
// helps:	application.php: <input id="delete-region"
{
	delete_box();
	user_region = "";
}

function add_region (file_string)
// helps:	here load_region()
{
//	alert (file_string);//DEBUG
	delete_box();
	var doc = null;
	try {
		doc = ge.parseKml (file_string);
		ge.getFeatures().appendChild (doc);
		var par = null;
		var geoms = ge.getElementsByType ("KmlMultiGeometry");
		if (! geoms.getLength())
			geoms = ge.getElementsByType ("KmlPolygon");
	//	if (! geoms.getLength())
	//		geoms = ge.getElementsByType ("KmlLinearRing"); //TODO: Wrap inside a Polygon, so PostGIS accepts it
		if (! geoms.getLength()) {
			alert ("File " + file_name + " has no valid region geometry." + "\n" + "It must contain a MultiGeometry or a Polygon.");
			the_zone = "";
		} else {
//			alert(geoms.item(0).getType());//DEBUG
			par = geoms.item(0).getParentNode();
			par.setStyleSelector (ge.createStyle (''));
			var ls = par.getStyleSelector().getLineStyle();
			ls.setWidth (query_line_width);
			ls.getColor().set (query_line_opa + query_line_BGR);
			var ps = par.getStyleSelector().getPolyStyle();
			ps.getColor().setA (query_fill_opa);
		//	pm_array.push (par);
		}
		if (doc.getAbstractView())	   ge.getView().setAbstractView (doc.getAbstractView());
		else
		if (par && par.getAbstractView())	   ge.getView().setAbstractView (par.getAbstractView());
		ge.getFeatures().removeChild (doc);
		if (par) ge.getFeatures().appendChild (par);
		box_pm = par;
	} catch (e) {
//		alert (e.name.toString());
		alert ("File " + file_name + " is not a KML file.");
	}
}
//		var poly = ge.createPolygon ('');
//		poly.setOuterBoundary (geoms.item(0));


function region_is_created() { return the_zone != ""; }

function xml_doc_from_file (file_string)
{
	if (window.DOMParser) {
		var parser = new DOMParser();
		var xd = parser.parseFromString (file_string, "text/xml");
	} else if (window.ActiveXObject) { // Internet Explorer
		var xd = new ActiveXObject ("Microsoft.XMLDOM");
		xd.async = false;
		xd.loadXML (file_string);
	} else {
	  alert('No XML parser found in this browser.');
	}
	return xd;
}

function xml_doc_to_zone (xml_doc)
{
	var geom = xml_doc.getElementsByTagName ("MultiGeometry")[0];
	if (! geom)
		geom = xml_doc.getElementsByTagName ("Polygon")[0];
	if (! geom)
		geom = xml_doc.getElementsByTagName ("LinearRing")[0];
	if (! geom) {
//		alert ("Document has no geometry");
		the_zone = "";
	}
	var serializer = new XMLSerializer();
	the_zone = serializer.serializeToString (geom);
//	alert (the_zone);//DEBUG
}
