// file:	chart_creation.js

function create_chart ()
// helps:	application.php
//			map_visualization.js: load_feature()
{
	var sim_choice = document.getElementById ('simulation-choice');
	var sim_text = sim_choice.options[sim_choice.selectedIndex].innerHTML;
	var sim_var =  sim_choice.options[sim_choice.selectedIndex].value;
	document.getElementById ('chart-div').innerHTML = '';
	xmlhttp = get_xmlhttp_request();
	xmlhttp.onreadystatechange = function()
	{
		var line;
		if (xmlhttp.readyState == RS_4_COMPLETE && xmlhttp.status == SC_200_OK) {
			var resp = xmlhttp.responseText;
//			alert(resp);//DEBUG // KEEP
			if (resp != '') {
				document.getElementById ('simulation-head').innerHTML = Growth_Yield_Head;
				document.getElementById ("chart-controls").style.display = "block";
				document.getElementById ('chart-div').      innerHTML = '';
				line = JSON.parse (resp);
				generate_chart (line, sim_text);
			} else {
				document.getElementById ('simulation-head').innerHTML = No_Simulation_Output;
				document.getElementById ('chart-div').      innerHTML = No_Simulation_Input;
			}
			document.getElementById ("progress-note").innerHTML = Ready;
		} else {
			set_simulation_lines (Busy_Icon + Space + Charting_Growth_Yield);
		}
	}
	var params = "variable=" + sim_var;
	xmlhttp.open ("POST",  PHP_Href + "/get_growth_yield.php" + "?" + params, true);
	xmlhttp.send();
}

function generate_chart (line, sim_text)
// helps:	create_chart()
{
	for (var i = 0; i < line.length; i++) {
		if (line[i][0] < Start_Year) {
			line.splice (i, 1);
			i--;
		}
	}
	var plot1 = $.jqplot ('chart-div', [line], {
		title: {
			text: sim_text,
			fontSize: '1.2em',
			textColor: '#009',
		},
		axesDefaults: {
			labelRenderer : $.jqplot.CanvasAxisLabelRenderer,
			tickRenderer: $.jqplot.CanvasAxisTickRenderer,
			tickOptions: { fontSize: '1.1em', textColor: '#00C', }
		},
		axes: {
			xaxis: {
				min: Min_Year, max: Max_Year,
//				label: 'Year',
			},
			yaxis: {
				min: 0,
//				label: sim_text,
			}
		},
		cursor : {
			show: true,
			zoom: true,
			showTooltip: false
		}
	});
}
