<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~nc2b6/cs3380/lab8/index.php');
	
	// Second, make sure we are not already logged in, if so, redirect to home.php, if not, display the registration form
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if ($logged_in) header('location: home.php');
	
	// Third, see if the form was already submitted, and if so, check information
	if (isset($_POST['submit'])){
		$username = $_POST['username'];
		$password = $_POST['password'];
		$c_password = $_POST['c_password'];
		
		// Prevent the addition of html special characters
		$username = htmlspecialchars($username);
		$password = htmlspecialchars($password);
		$c_password = htmlspecialchars($c_password);
		
		if ($password !== $c_password) $c_password_error = true; // Make sure password and confirm password are the same
		
		// Connect to the database
		include("../../secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
		
		// Make sure that the username is available (does not already exist)
		$name = pg_prepare($conn, 'username', "SELECT username FROM lab8.user_info WHERE username=$1;")
			or die("Failed to create username check query");
		$name = pg_execute($conn, 'username', array($username)) // Registration date will default to now
			or die("Failed to execute username check query");
		if (pg_num_rows($name) > 0) $username_error = true;
		
		if (!$c_password_error && !$username_error){			
			mt_srand(); // Seed number generator
			$salt = mt_rand();
			$hash = sha1($salt . $password);
			
			// Insert appropriate data into authentication user info, this must be first as authentication depends on this!
			$info = pg_prepare($conn, 'user_info', "INSERT INTO lab8.user_info (username) VALUES ($1);")
				or die("Failed to create user info query");
			$info = pg_execute($conn, 'user_info', array($username)) // Registration date will default to now
				or die("Failed to execute user info query");
			
			// Insert appropriate data into authentication table
			$auth = pg_prepare($conn, 'authentication', 'INSERT INTO lab8.authentication VALUES ($1, $2, $3);')
				or die("Failed to create authentication query");
			$auth = pg_execute($conn, 'authentication', array($username, $hash, $salt))
				or die("Failed to execute authentication query");
				
			// Record this registration into the log
			$log = pg_prepare($conn, 'log', "INSERT INTO lab8.log (username, ip_address, action) VALUES ($1, $2, 'registration');")
				or die("Failed to create log query");
			$log = pg_execute($conn, 'log', array($username, $_SERVER['REMOTE_ADDR'])) // We will use default values for log_id and log_date
				or die("Failed to execute log query");
				
			// Start session and redirect to home.php
			//session_start();
			$_SESSION['login'] = $username;
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
		<div class="form-group <?php if ($username_error) echo 'has-error'; ?>">
			<label for="username" class="control-label">Username</label>
			<input id="username" type="text" name="username" class="form-control" value="<?php echo $username; ?>" required>
			<?php if ($username_error) echo '<p class="help-block">There is already an account with this username!</p>'; ?>
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