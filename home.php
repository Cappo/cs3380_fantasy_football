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
        <?php
			switch($_SESSION['state']){
				case 0:
					echo '<p>Team creation!</p><br><a href="draft.php" class="btn btn-default">Go to draft</a>';
					break;
				case 1:
					echo '<p>Draft!</p><br><a href="draft.php" class="btn btn-default">Back to draft</a>';
					break;
				case 2:
					echo '<p>Week #!</p><br><a href="#" clas="btn btn-default">Next week</a>';
					break;
			}
		?>
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
	
	<?php if($_SESSION['state'] == 0){ ?><a href="create_team.php" class="btn btn-default">Create Team</a><?php } ?>
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
