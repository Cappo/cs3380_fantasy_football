<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~cs3380f14grp10');
	
	// Second, make sure we are logged in, if not, redirect to index.php
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if (!$logged_in) header('location: index.php');
	
	// Connect to the database
	include("secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
	
	// Get the team we are looking at
	$team = pg_prepare($conn, 'team', "SELECT * FROM master.team WHERE league=$1 AND team_id=$2;")
		or die("Failed to create team fetch query");
	$team = pg_execute($conn, 'team', array($logged_in, $_GET['team']))
		or die("Failed to execute team fetch query");
	if (pg_num_rows($team) < 1) $team_error = true;
	else $team = pg_fetch_array($team, NULL, PGSQL_ASSOC);
	
	// Display the login form
	include_once('_SNIPPETS/head.php');
?>
<div class="container">
<?php
if (!$team_error){
?>
	<div class="jumbotron">
		<h1><?php echo $team['name']; ?></h1>
		<p>Total Points: <?php echo $team['points']; ?></p>
		<p><?php echo $team['about']; ?></p>
	</div>
	<div class="page-header">
		<h1>Players</h1>
	</div>
	<?php
	// Fetch players for team
	$players = pg_prepare($conn, 'team_players', "SELECT player_id FROM master.draft WHERE league=$1 AND team_id=$2;")
		or die("Failed to create players fetch query");
	$players = pg_execute($conn, 'team_players', array($logged_in,intval($_GET['team'])))
		or die("Failed to execute players fetch query");
	display_table($players);
	?>
</div>
<?php
} else {
	echo '<p class="alert alert-danger">No team with that id found in this league!</p>';	
}
?>
<?php
	include_once('_SNIPPETS/footer.php');
	
	function display_table($result){ // Function that dynamically displays a table with the $result information
			$num_rows = pg_num_rows($result);
			echo "<p class='help-block text-center'>There were <strong>$num_rows</strong> rows returned.</p>";
			echo '<table class="table">';
			$row = pg_fetch_array($result, NULL, PGSQL_ASSOC);
			// First create table headers
			echo '<thead><tr>';
			foreach ($row as $key => $value){
					echo '<th>'.$key.'</th>';
			}
			echo '</tr></thead>';
			// Create rows of data
			echo '<tbody>';
			do {
				echo '<tr>';
				foreach ($row as $key => $value){
						echo '<td>'.$value.'</td>';
				}
				echo '</tr>';
			} while($row = pg_fetch_array($result, NULL, PGSQL_ASSOC));
			echo '</tbody></table>';
		}
?>
