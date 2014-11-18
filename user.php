<?php
	//get value from the dropdown box
	$q = strval($_GET['q']);
	
	//connect to database
	include("secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
	
	//sql uses the value from dropdown for the position
	$sql = "select * from gamedb.test 
			inner join gamedb.s2008
			on gamedb.test.id = gamedb.s2008.id
			where gamedb.test.pos = $1
			order by gamedb.test.fname asc";
	$result = pg_prepare($conn,"players",$sql) or die('Could Not Prepare'.pg_last_error());
	$result = pg_execute($conn,"players",array($q)) or die('Could Not Execute'.pg_last_error());
	
	//the echos are long, but I took out POS column....might haven taken more out cant remember 
	echo "<table align='center' class='table table-hover table-striped table-bordered'>
		<tr>
		<th style='text-align:center'>First</th>
		<th style='text-align:center'>Last</th>
		<th style='text-align:center'>Team</th>
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
