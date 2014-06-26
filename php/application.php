<?php
// file:	application.php G
// helps:	linkedin_login.js start_modelling()


	require "settings.php";
	require "db_connection.php";
	require "special_fids.php";
	session_start();
	$_SESSION ['user_id'] =     isset ($_REQUEST['user_id']) ?     $_REQUEST['user_id'] : null;
	$_SESSION ['first_name'] =  isset ($_REQUEST['first_name']) ?  $_REQUEST['first_name'] : null;
	$_SESSION ['last_name'] =   isset ($_REQUEST['last_name']) ?   $_REQUEST['last_name'] : null;
	$_SESSION ['os'] =   isset ($_REQUEST['os']) ?   $_REQUEST['os'] : null;
	$_SESSION ['browser'] =   isset ($_REQUEST['browser']) ?   $_REQUEST['browser'] : null;
	$_SESSION ['symbolizing'] = Default_Symbolizing;
	$_SESSION ['symbol_count_limit'] = Default_Symbol_Limit;
	build_years();
	build_variable_products();
	build_variable_weights();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=9">
		<title><?php echo App_Name ?> | Modeling</title> 
		<meta name="robots" content="noindex">
		<link type="image/vnd.microsoft.icon" rel="shortcut icon" href="<?php echo Images_Href; ?>/GaiaStudio.ICO">
		<link type="text/css" rel="stylesheet" href="<?php echo Styles_Href; ?>/app.css">
		<link type="text/css" rel="stylesheet" href="<?php echo Plugins_Href; ?>/jquery.jqplot.min.css">
		<link type="text/css" rel="stylesheet" href="<?php echo SynHigh_Href; ?>/styles/shCoreDefault.min.css">
		<link type="text/css" rel="stylesheet" href="<?php echo SynHigh_Href; ?>/styles/shThemejqPlot.min.css">
		<script type="text/javascript" src="https://www.google.com/jsapi"></script> <!-- TODO: Get Google API key? -->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]--> <!-- TODO: Get excanvas.js? -->
		<script type="text/javascript" src="<?php echo SynHigh_Href; ?>/scripts/shCore.min.js"></script>
		<script type="text/javascript" src="<?php echo SynHigh_Href; ?>/scripts/shBrushJScript.min.js"></script>
		<script type="text/javascript" src="<?php echo SynHigh_Href; ?>/scripts/shBrushXml.min.js"></script>
		<script type="text/javascript" src="<?php echo Plugins_Href; ?>/jquery.jqplot.min.js"></script>
		<script type="text/javascript" src="<?php echo Plugins_Href; ?>/jqplot.logAxisRenderer.min.js"></script>
		<script type="text/javascript" src="<?php echo Plugins_Href; ?>/jqplot.canvasTextRenderer.min.js"></script>
		<script type="text/javascript" src="<?php echo Plugins_Href; ?>/jqplot.canvasAxisLabelRenderer.min.js"></script>
		<script type="text/javascript" src="<?php echo Plugins_Href; ?>/jqplot.canvasAxisTickRenderer.min.js"></script>
		<script type="text/javascript" src="<?php echo Plugins_Href; ?>/jqplot.dateAxisRenderer.min.js"></script>
		<script type="text/javascript" src="<?php echo Plugins_Href; ?>/jqplot.categoryAxisRenderer.min.js"></script>
		<script type="text/javascript" src="<?php echo Plugins_Href; ?>/jqplot.barRenderer.min.js"></script>
		<script type="text/javascript" src="<?php echo Plugins_Href; ?>/jqplot.cursor.min.js"></script>
		
		<script type="text/javascript" src="<?php echo JS_Href; ?>/detect_browser.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/constants.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/settings.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/misc.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/init.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/chart_creation.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/modal_window.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/box_query.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/region_upload.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/attribute_query.js"></script>
		<script type="text/javascript" src="<?php echo JS_Href; ?>/map_visualization.js"></script>
	</head>
	<body onload="init_app (
<?php echo Project_Year; ?>, '<?php echo PHP_Href; ?>', '<?php echo Images_Href; ?>', '<?php echo Org_Units_KML; ?>', '<?php echo ini_long(); ?>', '<?php echo ini_lat(); ?>', '<?php echo ini_range(); ?>'
)">
		<div id="debug-div">
		</div>
		<iframe id="mask-iframe" scrolling="yes"></iframe>
		<div id="boxes-div">
			<div id="modal-dialog" class="window">
				<h3>Sample List of Features having chosen Age</h3>
				<p>To display a feature, click its ID. To select a different age, click <a href="#" class="close"><B>Close</B></a>.
				<div id='feature-listing'></div>
			</div>
			<div id="article-container">
				<article id="main-article">
					<section id="map-section">
						<div id="map-div"></div>
						<p id="versions-etc">
							<?php echo App_Host; ?>
							&nbsp; <?php echo App_Name.' '.App_Version; ?>	
							&nbsp; <?php echo ' User '.   $_SESSION ['user_id']; ?>	
							&nbsp; &ndash; &nbsp; <span id="plugin-version"></span>
					</section><!-- map-section -->
					<section id="controls-section">
						<table id="controls-subsection">
							<tr><td>
								<nav>
									<a target="_blank" href="<?php echo About_Page; ?>"> <input type="button" value="About..." title="Learn about <?php echo App_Name; ?>"></a>
									<a                 href="<?php echo Login_Page; ?>"> <input type="button" value="Logout"   title="Stop modeling"></a>
									<a target="_blank" href="<?php echo Help_Page; ?>">  <input type="button" value="Help..."  title="Get help on using <?php echo App_Name; ?>"></a>
									<a target="_blank" href="<?php echo Contact_Page; ?>"><input type="button" value="Contact..." title="Contact us -- GaiaStudio Software Inc"></a>
								</nav>
								<a id="company-logo" target="_blank" href="<?php echo About_Page; ?>" title="Learn about <?php echo App_Name; ?>"><img src="<?php echo Images_Href; ?>/GaiaStudio.PNG" height="50"></a>
								<br><small><?php echo Caveat; ?></small>
								<hr>
								<form id="box-settings">
									<label>Use 3D tree symbols<input id="3d-symbolizing" type="checkbox" name="box-settings" value="3d-symbolizing" <?php echo symbolizing() ? "checked" : ""; ?>></label>
									&nbsp;
									<label>Symbol count limit<input id="symbol-limit" type="number" name="symbol-limit" placeholder="<?php echo symbol_count_limit() ?>" value="<?php echo symbol_count_limit() ?>" min="1000" max="100000" step="1000"></label>
								</form>
								
								<h4>Search by Feature ID</h4>
								<label><input id="fid-txt" name="fid-txt" size="7"> Feature ID</label>
								<select id="fid-sel" name="fid-sel" onchange="document.getElementById ('fid-txt').value = '';">
									<option value=""></option>
									<?php
										while ($fid = array_pop ($special_fids)) 
											echo '<option value="'.$fid.'">'.$fid.'</option>';
									?>
								</select>
								<input id="disp-feature"    type="button" value="Display the Feature" onclick="query_database ('fid');">
								<input id="delete-features" type="button" value="Delete Results"      onclick="delete_features();">
							</td></tr>
							<tr><td>
								
 								<!--
                                                                <h4>Search by Simple Box</h4>
								<input id="create-box"   type="button" value="Create Query Box"     onclick="create_box();"> 
								<input id="larger-box"   type="button" value="Larger  +"            onclick="change_box ('+');">
								<input id="smaller-box"  type="button" value="Smaller &minus;"      onclick="change_box ('-');">
								<input id="display-box"  type="button" value="Display Box Features" onclick="query_database ('box');">
								<input id="delete-box"   type="button" value="Delete Query Box"     onclick="delete_box();"> 
                 -->    
								<h4>Search by Drawn Polygon</h4>
								<input id="draw-polygon" type="button" value="Draw Query Polygon"    onclick="create_drawn_poly();">
								<input id="display-poly" type="button" value="Display Polygon Features" onclick="use_drawn_poly();">
								<input id="delete-poly"  type="button" value="Delete Query Polygon"  onclick="delete_drawn_poly();">
							</td></tr>
							<tr><td>
							
								<?php
									if ($_SESSION ['browser'] == "Explorer") 
										require "attribute_search.php";
									else
										require "region_search.php";
								?>
							</td></tr>
							<tr><td>
							
								<progress id="progress-control" value="0" max="100">&nbsp;</progress>
								<p id="progress-note">&nbsp;</p>

								<h3 id="query-head"></h3>
								<small id="query-note"></small>

								<h4 id="profile-head"></h4>	
								<span id="download-map"></span> <span id="view-table"></span>
								<iframe id="results-frame" src="results_table.php" width="100%">You need a browser that supports iframes</iframe>
							</td></tr>
							<tr><td>
							
								<h4 id="simulation-head"></h4>
								<div id="chart-controls" style="display: none;">
									Variable
									<select id="simulation-choice">
										<option value="age"> age</option>
										<option value="dhgt">site height (m)</option>
										<option value="lhgt">Lorey height (m)</option>
										<option value="dia"> quadratic mean diameter (cm)</option>
										<option value="tph"> density (stems/ha)</option>
										<option value="ba">  basal area (m&sup2;/ha)</option>
										<option value="vws"> whole stem volume (m&sup3;/ha)</option>
										<option value="vcu"> close utilization volume (m&sup3;/ha)</option>
										<option value="vd">  close utilization volume net decay (m&sup3;/ha)</option>
										<option value="vdw"> close utilization volume net decay + waste (m&sup3;/ha)</option>
										<option value="vdwb" selected>
										                     close utilization volume net decay + waste + breakage (m&sup3;/ha)</option>
									</select>
									<input type="button" value="Generate Chart" onclick="create_chart()">
								</div>
							</td></tr>
						</table><!-- controls-subsection -->
						<table id="chart-subsection">
							<tr><td style="position: relative;">
								<div id="chart-div"></div>
							</td></tr>
						</table><!-- chart-subsection -->
					</section><!-- controls-section -->
				</article><!-- main-article -->
			</div><!-- article-container -->
		</div><!-- boxes-div -->
	</body>
</html>
