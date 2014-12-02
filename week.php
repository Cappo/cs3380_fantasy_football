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
	// First select teams
	// For each team
		// calculate points
		// join gamedb with draft such that draft team_id = team.id
		// FOR each player
			// update team set points=points+player[points]
	$teams = pg_prepare($conn, 'get_teams', "SELECT team_id FROM master.team WHERE league=$1;") or die("Failed to create get teams query");
	$teams = pg_execute($conn, 'get_teams', array($logged_in)) or die("Failed to execute state update query");
	$team = pg_fetch_array($teams, NULL, PGSQL_ASSOC);
	do{
		$players = pg_prepare($conn, 'get_players', "SELECT points FROM gamedb.gameweek$1 RIGHT JOIN (SELECT * FROM master.draft WHERE master.draft.team_id=$2) AS Draft ON gamedb.gameweek$1.id=Draft.player_id;") or die("Failed to create get players query".pg_last_error());
		$players = pg_execute($conn, 'get_players', array($_SESSION['week'],$team['team_id'])) or die("Failed to execute get players query".pg_last_error());
		$player = pg_fetch_array($players, NULL, PGSQL_ASSOC);
		do {
			$points = pg_prepare($conn, 'put_points', "UPDATE master.team SET points=points+$1 WHERE master.team.team_id=$2;") or die("Failed to points get players query".pg_last_error());
			$points = pg_execute($conn, 'put_points', array($player['points'],$team['team_id'])) or die("Failed to execute points update query".pg_last_error());
		} while ($player = pg_fetch_array($players, NULL, PGSQL_ASSOC));
	} while ($team = pg_fetch_array($teams, NULL, PGSQL_ASSOC));
	
	
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
		$_SESSION['state'] = 3;
	}
	header('location:index.php');
?>