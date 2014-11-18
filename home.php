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
	<div class="page-header">
		<h1>Home</h1>
	</div>
	<p>Welcome, <?php echo $logged_in; ?>!</p>
	<?php
	// Display IP address and registration date
	// Connect to the database
	include("secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
	
	echo "IP Address: " . $_SERVER['REMOTE_ADDR'] . "<br>";
	$registration = pg_prepare($conn, 'registration_date', "SELECT registration_date FROM master.user_info WHERE league=$1;")
		or die("Failed to create registration fetch query");
	$registration = pg_execute($conn, 'registration_date', array($logged_in))
		or die("Failed to execute registration fetch query");
	$registration = pg_fetch_array($registration, NULL, PGSQL_ASSOC);
	echo "Registration date: " . $registration['registration_date'] . "<br>";	
	?>
	<br>
	<?php
	// Fetch teams for league
	$teams = pg_prepare($conn, 'league_teams', "SELECT name AS Name,points AS Points,num_players AS Players,about AS About FROM master.team WHERE league=$1;")
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
