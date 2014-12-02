<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~cs3380f14grp10');
	
	// Second, make sure we are logged in, if not, redirect to index.php
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if (!$logged_in) header('location: index.php');
	
	if ($_SESSION['state'] != 2) header('location:index.php');
	
	// Connect to the database
	include("secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
	
	// First make sure we aren't trying to trade to the same team, if so, send error
	if (isset($_POST['submit_teams'])){
		if ($_POST['team1'] == $_POST['team2']) $team_error = true;
	} else if (isset($_POST['submit_players'])){
		// Trade the players (swap team_id in master.draft table)
		$update1 = pg_prepare($conn, 'update1', "UPDATE master.draft SET team_id=$1 WHERE team_id=$2 AND player_id=$3;") or die("Failed to create draft team query".pg_last_error());
		$update1 = pg_execute($conn, 'update1', array($_POST['team_id2'],$_POST['team_id1'],$_POST['player2'])) or die("Failed to execute draft team query".pg_last_error());
		$update2 = pg_prepare($conn, 'update2', "UPDATE master.draft SET team_id=$1 WHERE team_id=$2 AND player_id=$3;") or die("Failed to create draft team query".pg_last_error());
		$update2 = pg_execute($conn, 'update2', array($_POST['team_id1'],$_POST['team_id2'],$_POST['player1'])) or die("Failed to execute draft team query".pg_last_error());
		$trade_success = true;
	}
	
	include_once('_SNIPPETS/head.php');
?>
<div class="container">
<?php
echo'<div class="jumbotron"><h1>Trade<br><small>Pick teams below</small></h1>';
if ($team_error) echo '<p class="alert alert-danger">You cannot trade to the same team!</p>';
echo'<form class="form" method="POST" action="trade.php">
		<input type="hidden" name="team_id1" value="'.$_POST['team1'].'">
		<input type="hidden" name="team_id2" value="'.$_POST['team2'].'">
		<div class="form-group">
			<select class="form-control" name="team1">';
			//select all teams, display
			$teams = pg_prepare($conn, 'trade_teams', "SELECT team_id,name FROM master.team WHERE league=$1;") or die("Failed to create draft team query".pg_last_error());
			$teams = pg_execute($conn, 'trade_teams', array($logged_in)) or die("Failed to execute draft team query".pg_last_error());
			while($team = pg_fetch_array($teams, NULL, PGSQL_ASSOC)){
			echo'<option value="'.$team['team_id'].'">'.$team['name'].'</option>';
			}
echo '		</select></div>
			<div class="form-group">
			<select class="form-control" name="team2">';
			$teams = pg_execute($conn, 'trade_teams', array($logged_in)) or die("Failed to execute draft team query".pg_last_error());
			while($team = pg_fetch_array($teams, NULL, PGSQL_ASSOC)){
			echo'<option value="'.$team['team_id'].'">'.$team['name'].'</option>';
			}
echo '		</select></div>
			<input class="btn btn-success" name="submit_teams" type="submit" value="Trade">
		</form>';
echo'</div>';
?>
	<div class="page-header">
		<h1>Trade</h1>
	</div>
<?php
if ($trade_success) echo '<p class="alert alert-success">Trade successful!</p>';
if (isset($_POST['submit_teams']) && !$team_error){
echo'
		<form class="form" method="POST" action="trade.php">
		<div class="form-group">
			<select class="form-control" name="player1">';
			$players = pg_prepare($conn, 'trade_players', "SELECT id,lname,fname FROM seasondb.season INNER JOIN (select player_id FROM master.draft WHERE league=$1 AND team_id=$2) AS Draft ON seasondb.season.id=Draft.player_id;") or die("Failed to create draft team query".pg_last_error());
			$players = pg_execute($conn, 'trade_players', array($logged_in,intval($_POST['team1']))) or die("Failed to execute draft team query".pg_last_error());
			while($player = pg_fetch_array($players, NULL, PGSQL_ASSOC)){
			echo'<option value="'.$player['id'].'">'.$player['lname'].', '.$player['fname'].'</option>';
			}
echo'		</select></div>
			<div class="form-group">
			<select class="form-control" name="player2">';
			$players = pg_execute($conn, 'trade_players', array($logged_in,intval($_POST['team2']))) or die("Failed to execute draft team query".pg_last_error());
			while($player = pg_fetch_array($players, NULL, PGSQL_ASSOC)){
			echo'<option value="'.$player['id'].'">'.$player['lname'].', '.$player['fname'].'</option>';
			}
echo'		</select></div>
			<input class="btn btn-warning" name="submit_players" type="submit" value="Trade">
		</form>';
}b
?>
		
		<br>
		<hr>
		<div id="txtHint"><b></b></div>
</div>
        
<?php
	include_once('_SNIPPETS/footer.php');
?>