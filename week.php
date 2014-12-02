<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~cs3380f14grp10');
	
	// Second, make sure we are logged in, if not, redirect to index.php
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if (!$logged_in) header('location: index.php');
	
	// If the game state is not right, then don't let us here!
	if ($_SESSION['state'] != 2) header('location:index.php');
	
	// Connect to the database
	include("secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
	
	// Calculate points!
	
	
	// Set next week
	$_SESSION['week']++;
	$update = pg_prepare($conn, 'update', "UPDATE master.user_info SET week=$2 WHERE league=$1;")
		or die("Failed to create state update query");
	$update = pg_execute($conn, 'update', array($logged_in,$_SESSION['week']))
		or die("Failed to execute state update query");
		
	// If end of season (16 weeks) go to next phase (win/lose)
	if ($_SESSION['week'] > 16){
		$update2 = pg_prepare($conn, 'update2', "UPDATE master.user_info SET state=3 WHERE league=$1;")
			or die("Failed to create state update query");
		$update2 = pg_execute($conn, 'update2', array($logged_in))
			or die("Failed to execute state update query");
	}
	header('location:index.php');
?>