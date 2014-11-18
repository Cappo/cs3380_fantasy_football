<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~cs3380f14grp10');
	
	// Second, make sure we are not already logged in, if so, redirect to home.php, if not, display the registration form
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if (!$logged_in) header('location: index.php');
	// If game state is not 0 we cannot create teams so redirect home
	if ($_SESSION['state'] != 0) header('location: home.php');
	
	// Third, see if the form was already submitted, and if so, check information
	if (isset($_POST['submit'])){
		$team = $_POST['team'];
		$about = $_POST['about'];
		
		// Prevent the addition of html special characters
		$team = htmlspecialchars($team);
		$about = htmlspecialchars($about);
		
		// Connect to the database
		include("secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die(pg_last_error());
		
		// Make sure that the team name is available (does not already exist)
		$name = pg_prepare($conn, 'team', "SELECT league FROM master.team WHERE name=$1 AND league=$2;")
			or die("Failed to create team check query");
		$name = pg_execute($conn, 'team', array($team, $logged_in)) // Registration date will default to now
			or die("Failed to execute team check query");
		if (pg_num_rows($name) > 0) $team_error = true;
		
		// Make sure that the about team section is no longer than 255 characters
		if (strlen($about) > 255) $about_error = true;
		
		if (!$about_error && !$team_error){
			
			// Insert team data into the team table
			$info = pg_prepare($conn, 'insert_team', "INSERT INTO master.team (name, league, about) VALUES ($1, $2, $3);")
				or die("Failed to create team query");
			$info = pg_execute($conn, 'insert_team', array($team, $logged_in, $about)) 
				or die("Failed to execute team query");
				
			// Upon successful insert, return to home
			header('location: home.php');
		}
		
	}
	
	// Display the login form
	include_once('_SNIPPETS/head.php');
	
?>

<div class="container">
	<div class="page-header">
		<h1>Create Team</h1>
	</div>
	<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" >
		<div class="form-group <?php if ($team_error) echo 'has-error'; ?>">
			<label for="team" class="control-label">Team Name</label>
			<input id="team" type="text" name="team" class="form-control" value="<?php echo $team; ?>" required>
			<?php if ($team_error) echo '<p class="help-block">There is already a team with this name in this league!</p>'; ?>
		</div>
		<div class="form-group <?php if ($about_error) echo 'has-error'; ?>">
			<label for="about" class="control-label">About Team</label>
			<textarea id="about" name="about" class="form-control" required>
				<?php echo $about; ?>
			</textarea>
			<?php if ($about_error) echo '<p class="help-block">This field is too long (Max: 255 characters)!</p>'; ?>
		</div>
        <input type="submit" name="submit" class="btn btn-success" value="Create Team" />
		<a href="home.php" class="btn btn-warning">Cancel</a>
	</form>
</div>
<?php
	include_once('_SNIPPETS/footer.php');
?>