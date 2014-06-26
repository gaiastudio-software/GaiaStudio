var PHP_Href;

function init_login (php_href)
// helps:	application.php: <body onload
{
	PHP_Href = php_href;
}

function on_linkedin_load()
{
 	IN.Event.on (IN, "auth",   function() { on_linkedin_login(); });
	IN.Event.on (IN, "logout", function() { on_linkedin_logout(); });
}

function on_linkedin_logout()
{
	IN.API.Parse();
}

function on_linkedin_login()
{
	IN.API.Profile ("me")
		.fields (["id", "firstName", "lastName", "pictureUrl", "headline", "industry", "publicProfileUrl"])

		.result (function (result)
		{
			submit_linkedin_data (result.values[0]);
		})
		.error (function (err)
		{
			if (typeof (err) != "undefined" && err != null) {
				alert (err.message + '\n -- ' + err.raw_message);
			}
		});
}

function set_login_form (response)
{
	if (response == 'true') {
		head = "Login success.";
		body = "Starting application...";
	} else {
		head = "Login failure.";
		body = response;
	}
	document.getElementById ("login-head").innerHTML = head;
	document.getElementById ("login-body").innerHTML = body;
}

function submit_linkedin_data (profile)
{
	var params = "id=" + profile.id + "&firstName=" + profile.firstName + "&lastName=" + profile.lastName
		+ "&pictureUrl=" + encodeURIComponent (profile.pictureUrl)
		+ "&headline=" + profile.headline + "&industry=" + profile.industry
		+ "&publicProfileUrl=" + encodeURIComponent (profile.publicProfileUrl)
		;
	xmlhttp = get_xmlhttp_request();
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == RS_4_COMPLETE && xmlhttp.status == SC_200_OK) {
			set_login_form (xmlhttp.responseText);
			if (xmlhttp.responseText == 'true')
				start_modelling (profile);
		}
	}
	xmlhttp.open ("POST",  PHP_Href + "/submit_linkedin_data.php" + "?" + params, true);
	xmlhttp.send ();
}

function start_modelling (profile)
{
	var params = "user_id=" + profile.id
		+ "&" + "first_name=" + profile.firstName + "&" + "last_name=" + profile.lastName
		+ "&" + "os=" + BrowserDetect.OS + "&" + "browser=" + BrowserDetect.browser
		;
	window.location.href = PHP_Href + "/application.php" + "?" + params;
}

function get_xmlhttp_request()
{
	var ref = null;
	if (window.XMLHttpRequest) {
		ref = new XMLHttpRequest();
	} else if (window.ActiveXObject) { // IE 6
		ref = new ActiveXObject("MSXML2.XMLHTTP.3.0");
	}
	return ref;
}
