<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~cs3380f14grp10');
	
	// Second, make sure we are not already logged in, if so, redirect to home.php, if not, display the login form
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if ($logged_in) header('location: home.php');
	
	// If the from was submitted, process information and log the user in if valid
	if (isset($_POST['submit'])){
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		// Prevent the addition of html special characters
		$username = htmlspecialchars($username);
		$password = htmlspecialchars($password);
		
		// Connect to the database
		include("/secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
		
		// First, try to find the authentication information for provided username.
		$name = pg_prepare($conn, 'username', "SELECT * FROM lab8.authentication WHERE username=$1;")
			or die("Failed to create username check query");
		$name = pg_execute($conn, 'username', array($username)) // Registration date will default to now
			or die("Failed to execute username check query");
		if (pg_num_rows($name) == 0) $username_error = true;
		
		if (!$username_error){
			// Do a local hash of the provided password and fetched salt and then check that hash against the password
			$fetch = pg_fetch_assoc($name);
			$salt = intval($fetch['salt']);
			$check_hash = sha1($salt . $password);
			
			if ($check_hash == $fetch['password_hash']){
				// Record this successful login to the log
				$log = pg_prepare($conn, 'log', "INSERT INTO lab8.log (username, ip_address, action) VALUES ($1, $2, 'login');")
					or die("Failed to create log query");
				$log = pg_execute($conn, 'log', array($username, $_SERVER['REMOTE_ADDR'])) // We will use default values for log_id and log_date
					or die("Failed to execute log query");
				
				// Start session and redirect to home.php
				$_SESSION['login'] = $username;
				header('location: home.php');
			} else {
				$password_error = true;
				// Record the unsuccessful login to the log
				$log = pg_prepare($conn, 'log', "INSERT INTO lab8.log (username, ip_address, action) VALUES ($1, $2, 'failed login');")
					or die("Failed to create log query");
				$log = pg_execute($conn, 'log', array($username, $_SERVER['REMOTE_ADDR'])) // We will use default values for log_id and log_date
					or die("Failed to execute log query");
			}
		}
	}
	
	// Display the login form
	include_once('_SNIPPETS/head.php');
	
?>
<div class="container">
	<div class="page-header">
		<h1>Login</h1>
	</div>
	<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" >
		<div class="form-group <?php if ($username && $username_error) echo 'has-error'; ?>">
			<label for="username" class="control-label">Username</label>
			<input id="username" type="text" name="username" class="form-control" value="<?php echo $username; ?>" required>
			<?php if ($username && $username_error) echo '<p class="help-block">Sorry, we have no record of this username in our database!</p>'; ?>
		</div>
		<div class="form-group <?php if ($password_error) echo 'has-error'; ?>">
			<label for="password" class="control-label">Password</label>
			<input id="password" type="password" name="password" class="form-control" required>
			<?php if ($password_error) echo '<p class="help-block">The password you provided was not correct!</p>'; ?>
		</div>
        <input type="submit" name="submit" class="btn btn-success" value="Login" />
		<a href="register.php" class="btn btn-default">Register</a>
	</form>
</div>
<?php
	include_once('_SNIPPETS/footer.php');
?>