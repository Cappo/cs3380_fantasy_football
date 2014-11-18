<?php
	//get user search box text
	$z= htmlspecialchars(strval($_GET['z']));

	//connect to database
	include("secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
	
	//sql to search first and last name 
	$sql = "select * from seasondb.season
			where seasondb.season.lname ilike ('%'||$1||'%')
			or seasondb.season.fname ilike ('%'||$2||'%')
			order by points desc";
	$result = pg_prepare($conn,"search_p",$sql) or die('Could Not Prepare'.pg_last_error());
	$result = pg_execute($conn,"search_p",array($z,$z)) or die('Could Not Execute'.pg_last_error());
	
	
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
		<th style='text-align:center'>Points</th>
		</tr>";
	echo '<tbody>';
	while($row = pg_fetch_array($result)){
		echo "<tr align='center'>";
		echo "<td>".$row['fname']."</td>";
		echo "<td>".$row['lname']."</td>";
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
		echo "<td>".$row['points']."</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
?>
