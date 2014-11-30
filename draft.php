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
	if ($_SESSION['state'] == 0){
		$update = pg_prepare($conn, 'update', "UPDATE master.user_info SET state=1,round=1 WHERE league=$1;")
			or die("Failed to create state update query");
		$update = pg_execute($conn, 'update', array($logged_in))
			or die("Failed to execute state update query");
		$_SESSION['state'] = 1;
		$_SESSION['draft'] = 1;
	}

	// Determine draft round and what team is drafting
	$draft = $_SESSION['draft'];
	$draft = $draft - 1;
	// Fetch teams for league
	$team = pg_prepare($conn, 'draft_team', "SELECT * FROM master.team WHERE league=$1 AND num_players=$2 ORDER BY turn_order DESC LIMIT 1;")
		or die("Failed to create draft team query");
	$team = pg_execute($conn, 'draft_team', array($logged_in, intval($draft)))
		or die("Failed to execute draft team query");
	$draft_team = pg_fetch_array($team, NULL, PGSQL_ASSOC);
	
	// If draft_team returned no rows then we have moved on to the next draft round!
	$num_rows = pg_num_rows($team);
	if ($num_rows == 0){
		$draft = $draft + 1;
		$_SESSION['draft'] = $draft;
		$draft = pg_prepare($conn, 'draft_update', "UPDATE master.user_info SET round=$1 WHERE league=$2;")
			or die("Failed to create draft update query");
		$draft = pg_execute($conn, 'draft_update', array($draft,$logged_in))
			or die("Failed to execute draft update query");
		// Now we need to get the next team in line, should start draft order over
		$team = pg_prepare($conn, 'draft_team', "SELECT * FROM master.team WHERE league=$1 AND num_players=$2 ORDER BY turn_order DESC LIMIT 1;")
			or die("Failed to create draft team query");
		$team = pg_execute($conn, 'draft_team', array($logged_in, intval($draft)))
			or die("Failed to execute draft team query");
		$draft_team = pg_fetch_array($team, NULL, PGSQL_ASSOC);
	}
	
	// If someone selected to draft a player, we must add that player draft to the database
	if (isset($_POST['submit'])){
		$player_id = $_POST['id'];
		// First update the draft table to show draft selection
		$sql = pg_prepare($conn, 'draft_player', "INSERT INTO master.draft VALUES ($1,$2,$3)")
			or die("Failed to create draft player query");
		$sql = pg_execute($conn, 'draft_player', array($player_id,intval($draft_team['team_id']),$logged_in))
			or die("Failed to execute draft player query");
		// !!!  This next part could eventually be replaced by an SQL trigger function  !!!
		// We must increment the number of players of the team
		$sql = pg_prepare($conn, 'draft_player', "INSERT INTO master.draft VALUES ($1,$2,$3)")
			or die("Failed to create draft player query");
		$sql = pg_execute($conn, 'draft_player', array($player_id,intval($draft_team['team_id']),$logged_in))
			or die("Failed to execute draft player query");
		// Now we need to get the next team in line
		$team = pg_prepare($conn, 'draft_team', "SELECT * FROM master.team WHERE league=$1 AND num_players=$2 ORDER BY turn_order DESC LIMIT 1;")
			or die("Failed to create draft team query");
		$team = pg_execute($conn, 'draft_team', array($logged_in, intval($draft)))
			or die("Failed to execute draft team query");
		$draft_team = pg_fetch_array($team, NULL, PGSQL_ASSOC);
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
			<option value="fb">FB</option>
			<option value="rb">RB</option>
			<option value="te">TE</option>
			<option value="p">P</option>
			<option value="lb">LB</option>
			</select>
		</form>
		
		<!--form for the search box-->
		<form style="float: right">
			<input class="form-control" placeholder="Search Name" type="text" size="30" onkeyup="showResult(this.value)">
		</form><br>
		<div id="txtHint"><b></b></div>
		<div id="livesearch"></div>
</div>
        
<?php
	include_once('_SNIPPETS/footer.php');
?>