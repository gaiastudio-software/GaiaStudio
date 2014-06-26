// file:	region_of_interest.js

var xmin, ymin, xmax, ymax;
var the_zone = "";

function create_box()
// helps:	application.php
{
	delete_box();
	var polygonPlacemark = ge.createPlacemark ('');
	var polygon = ge.createPolygon ('');
	polygonPlacemark.setGeometry (polygon);
	var outer = ge.createLinearRing ('');
	polygon.setOuterBoundary (outer);
	var center = ge.getView().copyAsLookAt (ge.ALTITUDE_RELATIVE_TO_GROUND);
	var coords = outer.getCoordinates();
	var lat = center.getLatitude();
	var lon = center.getLongitude();
	coords.pushLatLngAlt (lat - Opolysize, lon - Opolysize, 0);
	coords.pushLatLngAlt (lat - Opolysize, lon + Opolysize, 0);
	coords.pushLatLngAlt (lat + Opolysize, lon + Opolysize, 0);
	coords.pushLatLngAlt (lat + Opolysize, lon - Opolysize, 0);
	xmin = lon - Opolysize;
	ymin = lat - Opolysize;
	xmax = lon + Opolysize;
	ymax = lat + Opolysize;
	var innerBoundary = ge.createLinearRing ('');
	polygon.getInnerBoundaries().appendChild (innerBoundary);
	coords = innerBoundary.getCoordinates();
	coords.pushLatLngAlt (lat - Ipolysize, lon - Ipolysize, 0);
	coords.pushLatLngAlt (lat - Ipolysize, lon + Ipolysize, 0);
	coords.pushLatLngAlt (lat + Ipolysize, lon + Ipolysize, 0);
	coords.pushLatLngAlt (lat + Ipolysize, lon - Ipolysize, 0);
	polygonPlacemark.setStyleSelector (ge.createStyle (''));
	var lineStyle = polygonPlacemark.getStyleSelector().getLineStyle();
	lineStyle.setWidth (5); // MAGIC
	lineStyle.getColor().set ('9900ffff'); // MAGIC
	ge.getFeatures().appendChild (polygonPlacemark);
	ppArray.push (polygonPlacemark);
	box_to_zone();
}

function box_to_zone()
// helps:	create_box()
{
	the_zone =
		'<Polygon><outerBoundaryIs><LinearRing><coordinates>'
		 + xmin +','+ ymin +' '+ xmin +','+ ymax +' '+ xmax +','+ ymax +' '+ xmax +','+ ymin +' '+ xmin +','+ ymin
		 + '</coordinates></LinearRing></outerBoundaryIs></Polygon>';
}

function change_box (op)
// helps:	application.php
{
	if (op == '+') {
		Opolysize = Opolysize * enlargement_factor;
		Ipolysize = Ipolysize * enlargement_factor;
	}
	if (op == '-') {
		Opolysize = Opolysize / enlargement_factor;
		Ipolysize = Ipolysize / enlargement_factor;
	}
	create_box();
}

function delete_box()
// helps:	application.php: <input id="delpoly"
{
	var kmlObjectList = ge.getFeatures().getChildNodes();
	for (var i = 0; i < ppArray.length; i++) {
		ge.getFeatures().removeChild (ppArray[i]);
	}
	the_zone = "";
}

function box_is_created() { return the_zone != ""; }
