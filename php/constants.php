<?php
// file:	constants.php
// helps:	many

define ("Revision",        "464.g"); //// <-------- CHANGE
define ("Minor_Version",     "0");
define ("Major_Version",     "0");
define ("Our_Site",        ""); ////// <-------- CHANGE; insert your website link here

define ("Root_Path",        ""); //// <-------- CHANGE
//define ("Root_Path",       ""); //// <-------- CHANGE

define ("App_Host",        Our_Site.Root_Path); //// <-------- CHANGE
//define ("App_Host",        "localhost".Root_Path); //// <-------- CHANGE

define ("KML_Folder",      'kml');
define ("Results_Folder",  'htm');

define ("Styles_Href",     Root_Path.'/css');
define ("JS_Href",         Root_Path.'/js');
define ("PHP_Href",        Root_Path.'/php');

define ("Outlines_Href",   'http://'.App_Host.'/kml');
define ("Org_Units_KML",   Outlines_Href.'/org_units.kml');

define ("Models_Href",     'http://'.Our_Site.'/models'); //// <-------- CHANGE
//define ("Models_Href",     'http://'.App_Host.'/models'); //// <-------- CHANGE

define ("Images_Href",     'http://'.Our_Site.'/images'); //// <-------- CHANGE
//define ("Images_Href",     'http://'.App_Host.'/images');  //// <-------- CHANGE

define ("About_Page",      '');  //// <-------- CHANGE; insert your About web-page here
//define ("About_Page",      PHP_Href.'/about.php');  //// <-------- CHANGE
define ("Help_Page",       PHP_Href.'/help.php');
define ("Login_Page",      PHP_Href.'/index.php');
define ("Contact_Page",    '');  //// <-------- CHANGE insert your contact web-page here

define ("Plugins_Href",    'http://'.Our_Site.'/plugins'); 
define ("SynHigh_Href",    'http://'.Our_Site.'/syntaxhighlighter'); 

define ("App_Version",     Major_Version.'.'.Minor_Version.'.'.Revision);

define ("DB_Host",         Our_Site); //// <-------- CHANGE
//define ("DB_Host",         "localhost"); //// <-------- CHANGE

define ("DB_Port",         ); //// <-------- CHANGE; insert your cloud-based PostGIS port here
//define ("DB_Port",         ); //// <-------- CHANGE  insert your local PostGIS port here

define ("DB_Pass",         ""); //// <-------- CHANGE Insert your cloud-based database password here
//define ("DB_Pass",         "postgres"); //// <-------- CHANGE Insert your local database password here

define ("VRI_DB",          "vri"); //// <-------- CHANGE
//define ("VRI_DB",          "vri"); //// <-------- CHANGE

define ("VRI_Table",       'sbcv'); //// <-------- CHANGE ---------- fd3 + vi
//define ("VRI_Table",       'fd3'); //// <-------- CHANGE
//define ("VRI_Table",       'vi'); //// <-------- CHANGE

define ("VDYP_Table",      'sbcy'); //// <-------- CHANGE ---------- fdy + vdyp
//define ("VDYP_Table",      'fdy'); //// <-------- CHANGE
//define ("VDYP_Table",      'vdyp'); //// <-------- CHANGE

define ("DB_User",         "");  <-------- CHANGE
define ("Users_DB",        "");  <-------- CHANGE

define ("VRI_Coverage",    
"The current inventory only covers parts of some southern BC Forest Districts: Sunshine Coast (DSC), South Island (DSI), Squamish (DSQ), North Island - Central Coast (DNI), Chilliwack (DCK), Okanagan Shuswap (DOS), and Kootenay Lake (DKL).");
//define ("VRI_Coverage",    "The current inventory covers Vancouver Island, BC and nearby parts of BC's lower mainland.");

define ("Project_Year",    2011);
define ("Yr_index_beg",       3); // PostgreSQL uses a one-based array indexing!
define ("Yr_index_end",      23);
define ("VDYP_Period",       10);

define ("XML_Line",        '<?xml version="1.0" encoding="UTF-8"?>');
define ("KML_Line",        '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2">');
define ("Feature_prefix",  "f_");
define ("Mapsheet_prefix", "m_");
define ("Zone_prefix",     "z_");
define ("Using_Existing_KML",  false);
define ("KML_Ext",         '.kml');
define ("Result_Ext",      '.htm');
define ("Data_Sch",        'data');
define ("Temp_Sch",        'temp');
define ("Caveat",          "");//<- remarks
define ("Main_Table",      Data_Sch.'.'.VRI_Table);
define ("Yield_Table",     Data_Sch.'.'.VDYP_Table);
define ("KML_SRID",        4326);
define ("Map_SRID",        3005);
define ("Kino_Factor",        7);
define ("KML_Precision",      5);
define ("KML_Format",     '%.'.KML_Precision.'f');
define ("Earth_Radius",    6371000);
define ("Our_Name",        "");//<- insert name
define ("App_Name",        "");//<-insert name
define ("Default_Symbol_Limit", 25000);
define ("Default_Symbolizing",  false);
//define ("Max_Symbol_Factor",    7);

