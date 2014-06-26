<?php
// file:	attribute_search.php
// helps:	application.php
?>
	<h4>Search by Attribute</h4>
	<label>Age
	<select id="age-sel" name="age-sel">
		<option value=""></option>
		<?php
			$query_str = "SELECT age FROM temp.ages";
			$query_res = pg_query (vri_connection(), $query_str) or die ("age query failed");
			while ($row = pg_fetch_array ($query_res)) echo '<option value="'.$row[age].'">'.$row[age].'</option>';
		?>
	</select></label>
	<a name="modal-anchor" href="#modal-dialog"><input type="button" value="List Features..." onclick="field_query_database()"></a>
