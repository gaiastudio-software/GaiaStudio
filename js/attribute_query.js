// file:	attribute_query.js
// TODO;:	Generalize/Expand to allow for multiple attribute criteria

function field_query_database()
// helps:	application.php
{
	var age = get_fage();
	if (age == null) {
		document.getElementById ('feature-listing').innerHTML = '<b>No age selected!</b>';
	} else {
		xmlhttp = get_xmlhttp_request();
		xmlhttp.onreadystatechange = function()
		{
			if (xmlhttp.readyState == RS_4_COMPLETE && xmlhttp.status == SC_200_OK) {
				document.getElementById ('feature-listing').innerHTML = xmlhttp.responseText;
			} else {
				document.getElementById ('feature-listing').innerHTML = Busy_Icon + Space + Searching_Database;
			}
		}
		xmlhttp.open ("POST",  PHP_Href + "/write_age_table.php?age=" + age, true);
		xmlhttp.send();
	}
}

function get_fage()
// helps:	field_query_database()
{
	var e = document.getElementById ('age-sel')
	if (e.options[e.selectedIndex].innerHTML == '') {
		return null;
	} else {
		return e.options[e.selectedIndex].innerHTML;
	}
}
