<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~cs3380f14grp10');
	
	// Second, make sure we are not already logged in, if so, redirect to home.php, if not, display the registration form
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if ($logged_in) header('location: home.php');
	
	// Third, see if the form was already submitted, and if so, check information
	if (isset($_POST['submit'])){
		$league = $_POST['league'];
		$password = $_POST['password'];
		$c_password = $_POST['c_password'];
		
		// Prevent the addition of html special characters
		$league = htmlspecialchars($league);
		$password = htmlspecialchars($password);
		$c_password = htmlspecialchars($c_password);
		
		if ($password !== $c_password) $c_password_error = true; // Make sure password and confirm password are the same
		
		// Connect to the database
		include("secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die(pg_last_error());
		
		// Make sure that the league is available (does not already exist)
		$name = pg_prepare($conn, 'league', "SELECT league FROM master.user_info WHERE league=$1;")
			or die("Failed to create league check query");
		$name = pg_execute($conn, 'league', array($league)) // Registration date will default to now
			or die("Failed to execute league check query");
		if (pg_num_rows($name) > 0) $league_error = true;
		
		if (!$c_password_error && !$league_error){			
			mt_srand(); // Seed number generator
			$salt = mt_rand();
			$hash = sha1($salt . $password);
			
			// Insert appropriate data into authentication user info, this must be first as authentication depends on this!
			$info = pg_prepare($conn, 'user_info', "INSERT INTO master.user_info (league) VALUES ($1);")
				or die("Failed to create user info query");
			$info = pg_execute($conn, 'user_info', array($league)) // Registration date will default to now
				or die("Failed to execute user info query");
			
			// Insert appropriate data into authentication table
			$auth = pg_prepare($conn, 'authentication', 'INSERT INTO master.authentication VALUES ($1, $2, $3);')
				or die("Failed to create authentication query");
			$auth = pg_execute($conn, 'authentication', array($league, $hash, $salt))
				or die("Failed to execute authentication query");
			
			// Start session and redirect to home.php
			session_start();
			$_SESSION['login'] = $league;
			header('location: home.php');
		}
		
	}
	
	// Display the login form
	include_once('_SNIPPETS/head.php');
	
?>
<div class="container">
	<div class="page-header">
		<h1>Register</h1>
	</div>
	<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" >
		<div class="form-group <?php if ($league_error) echo 'has-error'; ?>">
			<label for="league" class="control-label">League</label>
			<input id="league" type="text" name="league" class="form-control" value="<?php echo $league; ?>" required>
			<?php if ($league_error) echo '<p class="help-block">There is already an account with this league!</p>'; ?>
		</div>
		<div class="form-group">
			<label for="password" class="control-label">Password</label>
			<input id="password" type="password" name="password" class="form-control" required>
		</div>
		<div class="form-group <?php if ($c_password_error) echo 'has-error'; ?>">
			<label for="c_password" class="control-label">Confirm Password</label>
			<input id="c_password" type="password" name="c_password" class="form-control" required>
			<?php if ($c_password_error) echo '<p class="help-block">This field did not match your password field!</p>'; ?>
		</div>
        <input type="submit" name="submit" class="btn btn-success" value="Register" />
		<a href="index.php" class="btn btn-default">Login</a>
	</form>
</div>
<?php
	include_once('_SNIPPETS/footer.php');
?>