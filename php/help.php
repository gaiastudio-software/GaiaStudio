<?php
// file:	help.php
// helps:	application.php, about.php, index.php
	require "settings.php";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo App_Name; ?> | Help</title>
	<link type="text/css" rel="stylesheet" href="<?php echo Styles_Href; ?>/app.css">
	<link type="image/vnd.microsoft.icon" rel="shortcut icon" href="<?php echo Images_Href; ?>/GaiaStudio.ICO">
</head>
<body>
	<article id="page-article">
		<header>
			<header>
				<a id="header-logo" target="_blank" href="<?php echo About_Page; ?>"><img src="<?php echo Images_Href; ?>/GaiaStudio.PNG"></a>
				<h3><?php echo App_Name; ?>: &nbsp; <em>Visionary Ecosystem Management</em></h3>
			</header>
			<nav>
				<a href="<?php echo About_Page; ?>"><input type="button" value="About" title="Learn about <?php echo App_Name; ?>"></a>
				<a href="<?php echo Login_Page; ?>"><input type="button" value="Login" title="Start modeling with <?php echo App_Name; ?>"></a>
				<a>                                 <input type="button" value="Help"  title="You're here"></a>
			</nav>
			<h1>Using <?php echo App_Name; ?></h1>
		</header>
		<section id="page-section">
			<p><?php echo Caveat; ?>
			<h2>Requirements</h2>
			<p>You must have a <b>LinkedIn</b> account in order to log in to <?php echo App_Name; ?>. If you don't yet have a LinkedIn account, click the LinkedIn button anyway; you can sign up for one right there.
			<p>On <b>Windows</b> you may use a recent <b>Chrome</b>, <b>Firefox</b> or <b>IE</b> (32-bit) web browser.
			On <b>Mac</b> OS X 10.6+, you may use <b>Safari</b> or other browsers.
			<?php echo App_Name; ?> will not work on Linux/Unix.
			<p>You must have the <b>Google Earth Plugin</b> installed, but you should <em>not</em> have the Google Earth <em>desktop</em> &ndash; or any other graphics-intensive process &ndash; running at the same time.
			If the Google Earth Plugin is not yet installed, you'll be asked to download it.

			<h2>Settings</h2>
			<p>Choose whether or not to <i>Show 3D tree symbols</i>. Turning/leaving this off (unchecked) dramatically reduces the computational load on your machine.
			<p>If you choose to show 3D symbols, also choose what the <i>Symbol count limit</i> should be.
			When the number of trees to be visualized is greater than the specified limit (the default is <?php echo Default_Symbol_Limit; ?>), a symbolization factor will come into play where each symbol will represent more than one tree.
			<figure>
				<img src="<?php echo Images_Href; ?>/user_settings.png" alt="" width="100%">
			</figure>
			
			<h2>Single-feature Query</h2>
			<p>The simplest queries are for individual features (VRI polygons).
			If you happen to know its ID, you can enter it under <b>Search by Feature ID</b>, or you can choose one from the drop-down list of sample feature IDs.
			<p>Click <i>Display Feature</i> and <?php echo App_Name; ?> will retrieve data, show some statistics and, if simulation data exist, show a 200 year future growth/yield chart.
			It will also zoom to that feature on the map and, if <i>Show 3D tree symbols</i> is turned on, render numerous 3D tree symbols.
			<figure>
				<img src="<?php echo Images_Href; ?>/growth_yield_chart.png" alt="" width="100%">
			</figure>
			<p>Pan, zoom or rotate around the feature via the standard <a target="_blank" href="http://www.google.com/earth/learn/beginner.html#tab=navigation" title="Navigating GE desktop">Google Earth controls</a>.
			Assuming simulation data do exist (though not all VRI polygons have enough data to seed the VDYP model) you may choose different variables to chart from the <i>Variable</i> list and click <i>Generate Chart</i>.
			If there is significant growth (e.g., from a young stand) you can move the "time slider" (top left) to see the trees grow/shrink.
			<figure>
				<img src="<?php echo Images_Href; ?>/three_dimensional_visualization.png" alt="" width="100%">
			</figure>

			<h2>Box Query</h2>
			<p>A more likely query is a "square" geographical query.
			Zoom in, via standard Google Earth controls, to where (on or near Vancouver Island) you want.
			<figure>
				<img src="<?php echo Images_Href; ?>/search_by_box.png" alt="" width="100%">
			</figure>
			<p>Click <i>Create Query Box</i> and a yellow query box appears.
			Click <i>Larger &plus;</i> or <i>Smaller &minus;</i> as required.
			If it's not in the right location, just pan to the right place then click <i>Create Query Box</i> again.
			<figure>
				<img src="<?php echo Images_Href; ?>/create_query_box.png" alt="" width="100%">
			</figure>
			<p>When ready, click <i>Display Features</i> (among the same button group) and <?php echo App_Name; ?> will retrieve data, show statistics, maybe show a growth/yield chart, and render 3D tree symbols for the exact set of features that intersect the query box.
			The upper table summarizes the whole query region and the lower table lists data for each feature within.
			<figure>
				<img src="<?php echo Images_Href; ?>/display_features_ready.png" alt="" width="100%">
			</figure>
			<p>Pan, zoom or rotate around the features.
			If simulation data exist, they are weighted averages of individual feature simulations.
			Choose different variables to chart from the <i>Variable</i> list and click <i>Generate Chart</i>.
			If there is significant growth, move the "time slider" to see the trees grow/shrink.

			<h2>Region Query</h2>
			<strong>Note: this function is not available using Internet Explorer.</strong>
			<p>Another likely query is a geographical query <em>specific</em> to your region of interest. 
			You can do this if your region (on or near Vancouver Island) is defined in a KML file on your machine, where your browser is running.
			<figure>
				<img src="<?php echo Images_Href; ?>/search_by_box.png" alt="" width="100%">
			</figure>
			<p>Click <i>Choose File</i>, navigate to the KML file on your computer and then "Open" it.
			Its name should appear beside the Choose File button.
			<figure>
				<img src="<?php echo Images_Href; ?>/choose_file.png" alt="" width="50%">
			</figure>
			<p>Click <i>Create Query Region</i> and a yellow query region appears.
			Is this your region of interest?
			<figure>
				<img src="<?php echo Images_Href; ?>/create_query_region.png" alt="" width="100%">
			</figure>
			<p>When ready, click <i>Display Features</i> (among the same button group) and <?php echo App_Name; ?> will retrieve data, show statistics, maybe show a growth/yield chart, and render 3D tree symbols for the exact set of features that intersect your query region.
			<figure>
				<img src="<?php echo Images_Href; ?>/display_region_features.png" alt="" width="100%">
			</figure>

			<h2>Full Table</h2>
			<p>If you want a more complete view of any results table, click <i>View Full Table...</i>.
			<figure>
				<img src="<?php echo Images_Href; ?>/view_full_table.png" alt="" width="100%">
			</figure>
			<p>The full table appears in another browser tab.
			<figure>
				<img src="<?php echo Images_Href; ?>/a_full_table.png" alt="" width="100%">
			</figure>
			<p>If you want a more permanant record of it, do one of the following;
			<ul>
			<li>Use your browser's <i>Save Page As...</i> function and save the data as an HTML file to your machine.
			<li>Use your browser's <i>Edit</i> tools to <i>Select All</i> the data and <i>Copy</i> the data to the computer's clipboard. Then open a spreadsheet program and <i>Paste</i> the clipboard contents onto a blank sheet and <i>Save</i> as a spreadsheet file.
			</ul>

			<h2>Download Visualization</h2>
			<p>If you want a more permanant record of any results map, including any 3D tree symbols and growth simulation that exist, click <i>Download KML File...</i>.
			<figure>
				<img src="<?php echo Images_Href; ?>/download_kml_file.png" alt="" width="100%">
			</figure>
			Save the KML file to your computer.
			<figure>
				<img src="<?php echo Images_Href; ?>/save_kml_file.png" alt="" width="50%">
			</figure>
			<p>Assuming you have the Google Earth <em>desktop</em> installed, you can then open and explore the saved visualization at your leisure.

			<h2>Caveats</h2>
			<p><?php echo Caveat; ?>
			<p>
			The application database is a spatial and thematic subset of the BC Ministry of Forest VRI (Vegetation Resource Inventory) database of 2011.
			It covers map sheets from the 1: 250,000 scale National Topographic System (NTS) that cover Vancouver Island and parts of the Lower Mainland: 102I, 092L, 092K, 092E, 092F, 092G, 092C and 092B.
			<p>Tree species are identified by their standard two- or three-letter BC MoF codes.
			Only the first and second leading species are visualized (i.e., shown using 3D tree models).
			Locations of individual trees are based only on VRI areas and densities.
			Only the first leading species tree growths are simulated (i.e., have future growth predicted).
			Regardless of the actual leading species present, currently, they are always represented by Douglas fir and Amabalis fir 3D symbols, respectively.
			<p>System speed is greatly affected by the size of the query box/region and the density of trees within; the graphics processing power of your computer; and the network connection speed.
			<p>Growth and Yield data are estimated using Console VDYP 7 &ndash; the Variable Density Yield Prediction program from the BC MoF that estimates growth and yield parameters of an unmanaged forest stand.
			Results are based on the quality and availability of data from the VRI, on the validity of the VDYP, and on our understanding of the VRI and VDYP.
			</p>

			<h2>Future Functionality</h2>
			<p>We are planning many more capabilities in the near future, such as the ability to 
			<ul>
			<li>load/save/share queries and results
			<li>query based on multiple attributes
			<li>view/hide thematic layers
			<li>list and visualize dead stems
			<li>use TIPSY or more sophisticated simulations
			</ul>
			Tell us what you'd like to see.
			</p>
		</section>
		<footer>
			Copyright 2013 <?php echo Our_Name ?>.
			&nbsp; <a href="mailto:contact@gaias2dio.com?subject=<?php echo App_Name; ?>" title="Contact <?php echo Our_Name ?>">Contact us</a>
			&nbsp; <?php echo App_Name.' '.App_Version; ?>
		</footer>
	</article>
</body>
</html>
