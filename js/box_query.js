// file:	box_query.js

var lon_min, lat_min, lon_max, lat_max;
var the_zone = "";
var center_lat;
var center_lon;
var box_placemark = null;
var box_lat = query_box_size;
var box_lon = "";

var first_point;
var drawn_placemark = null;
var outer_bound=null;
var drawn_polygon=null;
var polygonIsDrawn=false;
var circlePlacemark=null;
var outerCircle=null;
var crossPlacemark=null;
var pointForCrossPlacemark=null;
var iconCross=null;
var styleCross=null;

function makeCircle(radius, center_lat, center_lon) {
  var ring = ge.createLinearRing('');
  var steps = 25;
  var pi2 = Math.PI * 2;
  for (var i = 0; i < steps; i++)
  {
    var lat = center_lat + radius * Math.cos(i / steps * pi2);
    var lng = center_lon + radius * Math.sin(i / steps * pi2);
    ring.getCoordinates().pushLatLngAlt(lat, lng, 0);
  }
  return ring;
}// end of makeCircle()


function eventHandlerForDrawnPoly(event)
{
		outer_bound.getCoordinates().pushLatLngAlt (event.getLatitude(), event.getLongitude(), 0);
		drawn_polygon.setOuterBoundary (outer_bound);
		drawn_placemark.setGeometry (drawn_polygon);

		drawn_placemark.setStyleSelector (ge.createStyle (''));
		var ls = drawn_placemark.getStyleSelector().getLineStyle();
		ls.setWidth (query_line_width);
		ls.getColor().set (query_line_opa + query_line_BGR);
		var ps = drawn_placemark.getStyleSelector().getPolyStyle();
		ps.getColor().setA (query_fill_opa);

		ge.getFeatures().appendChild (drawn_placemark);
		if (first_point == "")
			first_point = event.getLongitude() + ',' + event.getLatitude();
		the_zone += event.getLongitude() + ',' + event.getLatitude() + ' ';

                //drawing a circle around the clicked point on the Globe.
               /* circlePlacemark = ge.createPlacemark('');
                circlePlacemark.setGeometry(ge.createPolygon(''));
                outerCircle = ge.createLinearRing('');
                circlePlacemark.getGeometry().setOuterBoundary(makeCircle(0.05, event.getLatitude(), event.getLongitude()));
                ge.getFeatures().appendChild(circlePlacemark);
                */

                //drawing a cross-hair placemark
                /*
                crossPlacemark=ge.createPlacemark('');
                    // Define a custom icon.
                    iconCross = ge.createIcon('');
                    iconCross.setHref('http://gaias2dio.com/images/cross-hairs.png');//should be changed to declared constant or proper reference
                    styleCross = ge.createStyle(''); //create a new style
                    styleCross.getIconStyle().setIcon(iconCross); //apply the icon to the style
                    crossPlacemark.setStyleSelector(styleCross); //apply the style to the placemark
                pointForCrossPlacemark = ge.createPoint('');
                pointForCrossPlacemark.setLatitude(event.getLatitude());
                pointForCrossPlacemark.setLongitude(event.getLongitude());
                crossPlacemark.setGeometry(pointForCrossPlacemark);
                ge.getFeatures().appendChild(crossPlacemark);
                */

}//end of eventHandlerForDrawnPoly()

function create_drawn_poly()
// helps:	application.php <input id="draw-polygon"
{
	delete_drawn_poly();
	drawn_placemark = ge.createPlacemark('');
	drawn_polygon = ge.createPolygon('');
	drawn_polygon.setAltitudeMode (ge.ALTITUDE_CLAMP_TO_GROUND);
	outer_bound = ge.createLinearRing ('');
	outer_bound.setAltitudeMode (ge.ALTITUDE_CLAMP_TO_GROUND);
	first_point = "";
	alert("To draw a polygon on the map, use your mouse to click on successive points, then click the 'Display Region Features' button.");
	the_zone = '<Polygon><outerBoundaryIs><LinearRing><coordinates>';

        google.earth.addEventListener (ge.getGlobe(), 'mousedown', eventHandlerForDrawnPoly);

}

function use_drawn_poly()
// helps:	application.php <input id="display-poly"
{
        if((the_zone=="") || (polygonIsDrawn==true))
        {
            alert("First select a polygon");
            return;
        }

        the_zone += first_point; // ensures: last = first
	the_zone += '</coordinates></LinearRing></outerBoundaryIs></Polygon>';
	console.log ("box_query.js use_drawn_poly() the_zone" + '\n\n' + the_zone);
	query_database ('box');

        //remove event listener for Mouse Down event. Otherwise, each time you click on the map, it will continue to draw a polygon
        google.earth.removeEventListener(ge.getGlobe(), 'mousedown', eventHandlerForDrawnPoly);
        polygonIsDrawn=true;
}

function create_box()
// helps:	application.php <input id="create-box"
{
    var center = ge.getView().copyAsLookAt (ge.ALTITUDE_RELATIVE_TO_GROUND);
	center_lat = center.getLatitude();
	center_lon = center.getLongitude();
	box_lon = box_lat / Math.cos (center_lat * rad_per_deg);
	add_box();
}

function change_box (op)
// helps:	application.php <input id="larger-box" <input id="smaller-box"
{
	if (op == '+') {
		box_lat = box_lat * enlargement_factor;
	}
	if (op == '-') {
		box_lat = box_lat / enlargement_factor;
	}
	box_lon = box_lat / Math.cos (center_lat * rad_per_deg);
	add_box();
}

function add_box()
// helps:	here: create_box(), change_box()
{
	delete_box();
	box_placemark = ge.createPlacemark ('');
	var poly = ge.createPolygon ('');
	var lr = ge.createLinearRing ('');
	lr.setTessellate(true);
	poly.setOuterBoundary (lr);
	var coords = lr.getCoordinates();
	lon_min = center_lon - box_lon;
	lat_min = center_lat - box_lat;
	lon_max = center_lon + box_lon;
	lat_max = center_lat + box_lat;
	coords.pushLatLngAlt (lat_min, lon_min, 0);
	coords.pushLatLngAlt (lat_min, lon_max, 0);
	coords.pushLatLngAlt (lat_max, lon_max, 0);
	coords.pushLatLngAlt (lat_max, lon_min, 0);
	box_placemark.setGeometry (poly);
	box_placemark.setStyleSelector (ge.createStyle (''));
	var ls = box_placemark.getStyleSelector().getLineStyle();
	ls.setWidth (query_line_width);
	ls.getColor().set (query_line_opa + query_line_BGR);
	var ps = box_placemark.getStyleSelector().getPolyStyle();
	ps.getColor().setA (query_fill_opa);
	ge.getFeatures().appendChild (box_placemark);
	box_to_zone();
}

function box_to_zone()
// helps:	here add_box()
{
	the_zone = '<Polygon><outerBoundaryIs><LinearRing><coordinates>'
		 + lon_min +','+ lat_min +' '+ lon_min +','+ lat_max +' '+ lon_max +','+ lat_max +' '+ lon_max +','+ lat_min +' '+ lon_min +','+ lat_min
		 + '</coordinates></LinearRing></outerBoundaryIs></Polygon>';
	console.log ("box_query.js box_to_zone() the_zone" + '\n\n' + the_zone);
}

function box_is_created() { return the_zone != ""; }

function delete_box()
// helps:	application.php: <input id="delete-box"
//			region_upload.js: delete_region(), create_zone()
//			here: create_drawn_poly()
{
	the_zone = "";
	if (box_placemark) ge.getFeatures().removeChild (box_placemark);
	box_placemark = null;
}

function delete_drawn_poly()
// helps:	application.php: <input id="delete-poly"
//			here: add_box()
{
	the_zone = "";
	if (drawn_placemark) ge.getFeatures().removeChild (drawn_placemark);
	drawn_placemark = null;
        polygonIsDrawn=false;
}
