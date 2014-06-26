// file:	constants.js
// helps:	many

var Space = '&nbsp;';
var Login_Success =         'Login success.';
var Login_Failure =         'Login failure.';
var Starting_App =          'Starting application...';

var Missing_GE_Plugin =     'This browser does not support the Google Earth Plugin.';
var Charting_Growth_Yield = 'Charting Growth-Yield...';
var Loading_Google_Earth =  'Loading Google Earth...';
var Loading_Table =         'Loading Table...';
var Loading_Visualization = 'Loading Visualization...';
var Meantime =              'Do browse the table or chart below...';
var Please_Wait =           'Please do not browse the map...';
var Ready =                 '';
var Searching_Database =    'Searching Database...';

var Growth_Yield_Head =     'Growth and Yield Simulation';
var No_Simulation_Output =  'No simulation results';
var No_Simulation_Input =   'Either the VRI data are inadequate for simulation, or no simulation has yet been made for this area.';

var Missing_Feature_ID =    'First search for a feature or select an ID.';
var Missing_Query_Box =     'First create a query box.';
var Missing_Query_File =    'First choose a file to load.';
var Missing_Query_Region =  'First create the query region.';

var Missing_File_API =      'This browser does not support the HTML5 File API.';
var Invalid_Geometry =      'It must contain a MultiGeometry or a Polygon.';
var Invalid_XML =      '';

var Start_Year =   2013;
var Min_Year =     2010;
var Max_Year =     2210;

switch (BrowserDetect.browser) {
case "Chrome":
	var Fix_Results_Rows =   4.2;
	var Max_Results_Rows =  10.8; // 3.3 - 9 plain
	break;
case "Firefox":
	var Fix_Results_Rows =   4.1;
	var Max_Results_Rows =  10.8; // 3.4 - 9 plain
	break;
case "Opera":
	var Fix_Results_Rows =   5.0;
	var Max_Results_Rows =  11.9; // 3 - 10 plain
	break;
case "Explorer":
default:
	var Fix_Results_Rows =   5.3;
	var Max_Results_Rows =  11.4; // 3 - 10 plain
	break;
}
var Ems_Per_Row =  1.4;

var GE_Version = 1;
var GM_Version = 2;

var RS_4_COMPLETE = 4;
var SC_200_OK =   200;
