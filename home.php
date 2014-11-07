<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~nc2b6/cs3380/lab8/index.php');
	
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
	include("../../secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
	
	echo "IP Address: " . $_SERVER['REMOTE_ADDR'] . "<br>";
	$registration = pg_prepare($conn, 'registration_date', "SELECT registration_date FROM lab8.user_info WHERE username=$1;")
		or die("Failed to create registration fetch query");
	$registration = pg_execute($conn, 'registration_date', array($logged_in))
		or die("Failed to execute registration fetch query");
	$registration = pg_fetch_array($registration, NULL, PGSQL_ASSOC);
	echo "Registration date: " . $registration['registration_date'] . "<br>";
	
	// Pull the log of the username and display it using my display_table function
	$log_data = pg_prepare($conn, 'display_log', "SELECT * FROM lab8.log WHERE username=$1;")
		or die("Failed to create display log query");
	$log_data = pg_execute($conn, 'display_log', array($logged_in)) // Registration date will default to now
		or die("Failed to execute display_log query");
	display_table($log_data);
	
	?>
	<br>
	<a href="logout.php" class="btn btn-danger">Logout</a>
	<a href="update.php" class="btn btn-success">Update</a>
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
