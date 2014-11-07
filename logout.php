<?php
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	
	// If user is logged in, log them out, else just shoot them back to the index page
	if ($logged_in){
		// Record this successful login to the log
		// Connect to the database
		include("../../secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
			
		
		$log = pg_prepare($conn, 'log', "INSERT INTO lab8.log (username, ip_address, action) VALUES ($1, $2, 'logout');")
			or die("Failed to create log query");
		$log = pg_execute($conn, 'log', array($_SESSION['login'], $_SERVER['REMOTE_ADDR'])) // We will use default values for log_id and log_date
			or die("Failed to execute log query");
						
		// Logout process, unset session variables and destroy the session
		$_SESSION = array(); // unset session variables
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', 1,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		session_destroy(); // destroy session
		header('location: index.php');
	} else header('location: index.php');
?>