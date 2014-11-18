<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~cs3380f14grp10');
	
	// Second, make sure we are logged in, if not, redirect to index.php
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if (!$logged_in) header('location: index.php');
	
	// Display the login form
	include_once('_SNIPPETS/head.php');
?>
<div class="container">
		<div class="jumbotron">
		<h1><?php echo $logged_in; ?></h1>
		<p>Here it might say, DRAFT TIME! Or display the week number</p>
		<p>This is also where there may be <span class="btn btn-default">Buttons</span> might be to progress to the next week or go to draft</p>
	</div>
	<div class="page-header">
		<h1>Teams</h1>
	</div>
	<?php
	// Connect to the database
	include("secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");

	// Fetch teams for league
	$teams = pg_prepare($conn, 'league_teams', "SELECT team_id,name AS Name,points AS Points,num_players AS Players,about AS About FROM master.team WHERE league=$1;")
		or die("Failed to create teams fetch query");
	$teams = pg_execute($conn, 'league_teams', array($logged_in))
		or die("Failed to execute teams fetch query");
	display_table($teams);
	?>
	
	<a href="create_team.php" class="btn btn-default">Create Team</a>
	<a href="logout.php" class="btn btn-danger">Logout</a>
</div>
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
				if ($key !== 'team_id')
					echo '<th>'.$key.'</th>';
			}
			echo '</tr></thead>';
			// Create rows of data
			echo '<tbody>';
			do {
				echo '<tr>';
				foreach ($row as $key => $value){
						if ($key == 'team_id')
							echo '<td><a href="team.php?team='.$value.'">';
						else if ($key == 'name')
							echo $value.'</a></td>';
						else
							echo '<td>'.$value.'</td>';
				}
				echo '</tr>';
			} while($row = pg_fetch_array($result, NULL, PGSQL_ASSOC));
			echo '</tbody></table>';
		}
?>
