<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~cs3380f14grp10');
	
	// Second, make sure we are logged in, if not, redirect to index.php
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if (!$logged_in) header('location: index.php');
	
	// Connect to the database
	include("secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("Failed to connect to the database");
	
	
	// If this is the first time seeing draft view, we must set the database to know we have gone into draft mode
	// First fetch number of teams in league, we can't start a draft with less than 2 teams!
	if ($_SESSION['state'] == 0){
		$update = pg_prepare($conn, 'update', "UPDATE master.user_info SET state=1,round=1 WHERE league=$1;")
			or die("Failed to create state update query");
		$update = pg_execute($conn, 'update', array($logged_in))
			or die("Failed to execute state update query");
		$_SESSION['state'] = 1;
		$_SESSION['draft'] = 1;
	}
	// If the game state is not right, then don't let us here!
	if ($_SESSION['state'] != 1) header('location:index.php');

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
	}
	
	// If draft_team returned no rows then we have moved on to the next draft round!
	
	if ($draft_team['num_players'] >= $_SESSION['draft']){
		$_SESSION['draft']++;
		$draft = pg_prepare($conn, 'draft_update', "UPDATE master.user_info SET round=$1 WHERE league=$2;")
			or die("Failed to create draft update query".pg_last_error());
		$draft = pg_execute($conn, 'draft_update', array(intval($_SESSION['draft']),$logged_in))
			or die("Failed to execute draft update query".pg_last_error());
		// Now we need to get the next team in line, should start draft order over
		$team = pg_execute($conn, 'draft_team', array($logged_in))
			or die("Failed to execute draft team query".pg_last_error());
		$draft_team = pg_fetch_array($team, NULL, PGSQL_ASSOC);
		
		// Check to see if that was our last round
		if ($_SESSION['draft'] > 4){
			$update2 = pg_prepare($conn, 'update2', "UPDATE master.user_info SET state=2,week=1 WHERE league=$1;")
				or die("Failed to create state update query");
			$update2 = pg_execute($conn, 'update2', array($logged_in))
				or die("Failed to execute state update query");
			$_SESSION['state'] = 2;
			$_SESSION['week'] = 1;
			header('location:index.php');
		}
	}
	
	include_once('_SNIPPETS/head.php');
?>
<div class="container">
	<div class="jumbotron">
		<?php
			echo '<h1>Draft<br>';
			echo '<small>Round '.$_SESSION['draft'].'</small></h1>';
			echo '<p>'.$draft_team['name'].'</p>';
		?>
	</div>
	<div class="page-header">
		<h1>Players</h1>
	</div>
		
		<!--dropdown which calls the function sending value of users choice-->
		<form id="choice" style="float: left">
			<select class="form-control" name="users" onchange="showDraft(this.value)">
			<option value="">Select a position:</option>
			<option value="qb">QB</option>
			<option value="wr">WR</option>
			<option value="rb">RB</option>
			<option value="te">TE</option>
			</select>
		</form>
		
		<br>
		<hr>
		<div id="txtHint"><b></b></div>
</div>
        
<?php
	include_once('_SNIPPETS/footer.php');
?>