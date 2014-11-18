<?php
	//get user search box text
	$z= htmlspecialchars(strval($_GET['z']));

	
	//connect to database

	
	//sql to search first and last name 
	$sql = "select * from gamedb.test
			inner join gamedb.s2008
			on gamedb.test.id = gamedb.s2008.id
			where gamedb.test.lname ilike ('%'||$1||'%')
			or gamedb.test.fname ilike ('%'||$2||'%')";
	$result = pg_prepare($db,"search_p",$sql) or die('Could Not Prepare'.pg_last_error());
	$result = pg_execute($db,"search_p",array($z,$z)) or die('Could Not Execute'.pg_last_error());
	
	
	//basically just copied this from user.php though using pg_field_name could be neater
	echo "<br><h4>Search Results: </h4>";
	echo "<table align='center' class='table table-hover table-striped table-bordered'>
		<tr>
		<th style='text-align:center'>First</th>
		<th style='text-align:center'>Last</th>
		<th style='text-align:center'>Team</th>
		<th style='text-align:center'>Pos</th>
		<th style='text-align:center'>G</th>
		<th style='text-align:center'>Comp</th>
		<th style='text-align:center'>ATT</th>
		<th style='text-align:center'>PassYD</th>
		<th style='text-align:center'>PassTD</th>
		<th style='text-align:center'>Inte</th>
		<th style='text-align:center'>Rush</th>
		<th style='text-align:center'>RushYD</th>
		<th style='text-align:center'>RushTD</th>
		<th style='text-align:center'>Rec</th>
		<th style='text-align:center'>RecYD</th>
		<th style='text-align:center'>RecTD</th>
		</tr>";
	echo '<tbody>';
	while($row = pg_fetch_array($result)){
		echo "<tr align='center'>";
		echo "<td>".$row['lname']."</td>";
		echo "<td>".$row['fname']."</td>";
		echo "<td>".$row['team']."</td>";
		echo "<td>".$row['pos']."</td>";
		echo "<td>".$row['g']."</td>";
		echo "<td>".$row['comp']."</td>";
		echo "<td>".$row['att']."</td>";
		echo "<td>".$row['passyd']."</td>";
		echo "<td>".$row['passtd']."</td>";
		echo "<td>".$row['inte']."</td>";
		echo "<td>".$row['rush']."</td>";
		echo "<td>".$row['rushyd']."</td>";
		echo "<td>".$row['rushtd']."</td>";
		echo "<td>".$row['rec']."</td>";
		echo "<td>".$row['recyd']."</td>";
		echo "<td>".$row['rectd']."</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
?>
