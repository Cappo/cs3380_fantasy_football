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
	
/*
	// Determine draft round and what team is drafting
	$draft = $_SESSION['draft'];
	$looking_for = $draft - 1;
	// Fetch teams for league
	$team = pg_prepare($conn, 'draft_team', "SELECT * FROM master.team WHERE league=$1 ORDER BY num_players ASC,turn_order DESC LIMIT 1;")
		or die("Failed to create draft team query".pg_last_error());
	$team = pg_execute($conn, 'draft_team', array($logged_in))
		or die("Failed to execute draft team query".pg_last_error());
	$num_rows = pg_num_rows($team);
	$draft_team = pg_fetch_array($team, NULL, PGSQL_ASSOC);
	
	// If someone selected to draft a player, we must add that player draft to the database
	if (isset($_POST['submit'])){
		$player_id = $_POST['player_id'];
		// First update the draft table to show draft selection
		$sql = pg_prepare($conn, 'draft_player', "INSERT INTO master.draft VALUES ($1,$2,$3)")
			or die("Failed to create draft player query");
		$sql = pg_execute($conn, 'draft_player', array($player_id,intval($draft_team['team_id']),$logged_in))
			or die("Failed to execute draft player query".pg_last_error());
		// !!!  This next part could eventually be replaced by an SQL trigger function  !!!
		// We must increment the number of players of the team
		$sql = pg_prepare($conn, 'update_team_players', "UPDATE master.team SET num_players=$1 WHERE team_id=$2;")
			or die("Failed to create update team players query".pg_last_error());
		$sql = pg_execute($conn, 'update_team_players', array(intval($draft_team['num_players'])+1,intval($draft_team['team_id'])))
			or die("Failed to execute update team players query".pg_last_error());
		// Now we need to get the next team in line
		$team = pg_execute($conn, 'draft_team', array($logged_in))
			or die("Failed to execute draft team query".pg_last_error());
		$num_rows = pg_num_rows($team);
		$draft_team = pg_fetch_array($team, NULL, PGSQL_ASSOC);
	}*/
	
	include_once('_SNIPPETS/head.php');
?>
<div class="container">
<?php
echo'<div class="jumbotron"><h1>Trade<br><small>Pick teams below</small></h1>';
echo'<form method="POST" action="trade.php">
			<select class="form-control" name="team1">';
			//select all teams, display
			$teams = pg_prepare($conn, 'trade_teams', "SELECT team_id,name FROM master.team WHERE league=$1;") or die("Failed to create draft team query".pg_last_error());
			$teams = pg_execute($conn, 'trade_teams', array($logged_in)) or die("Failed to execute draft team query".pg_last_error());
			while($team = pg_fetch_array($teams, NULL, PGSQL_ASSOC)){
			echo'<option value="'.$team['team_id'].'">'.$team['name'].'</option>';
			}
echo '		</select>
			<select class="form-control" name="team2">';
			$teams = pg_execute($conn, 'trade_teams', array($logged_in)) or die("Failed to execute draft team query".pg_last_error());
			while($team = pg_fetch_array($teams, NULL, PGSQL_ASSOC)){
			echo'<option value="'.$team['team_id'].'">'.$team['name'].'</option>';
			}
echo '		</select>
			<input class="btn btn-success" name="submit_teams" type="submit" value="Trade">
		</form>';
echo'</div>';
?>
	<div class="page-header">
		<h1>Trade</h1>
	</div>
<?php
if (isset($_POST['submit_teams'])){
echo'
		<form method="POST" action="trade.php">
			<select class="form-control" name="player1">';
			$players = pg_prepare($conn, 'trade_players', "SELECT id,lname,fname FROM seasondb.season INNER JOIN (select player_id FROM master.draft WHERE league=$1 AND team_id=$2) AS Draft ON seasondb.season.id=Draft.player_id;") or die("Failed to create draft team query".pg_last_error());
			$players = pg_execute($conn, 'trade_players', array($logged_in,intval($_POST['team1']))) or die("Failed to execute draft team query".pg_last_error());
			while($player = pg_fetch_array($players, NULL, PGSQL_ASSOC)){
			echo'<option value="'.$player['id'].'">'.$player['lname'].', '.$player['fname'].'</option>';
			}
echo'		</select>
			<select class="form-control" name="player2">';
			$players = pg_execute($conn, 'trade_players', array($logged_in,intval($_POST['team_id']))) or die("Failed to execute draft team query".pg_last_error());
			while($player = pg_fetch_array($players, NULL, PGSQL_ASSOC)){
			echo'<option value="'.$player['id'].'">'.$player['lname'].', '.$player['team2'].'</option>';
			}
echo'		</select>
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