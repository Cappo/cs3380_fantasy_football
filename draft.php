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

	$draft = $_SESSION['draft'];
	$draft = $draft - 1;
	// Fetch teams for league
	$team = pg_prepare($conn, 'draft_team', "SELECT * FROM master.team WHERE league=$1 AND num_players=$2 ORDER BY turn_order DESC LIMIT 1;")
		or die("Failed to create draft team query");
	$team = pg_execute($conn, 'draft_team', array($logged_in, intval($draft)))
		or die("Failed to execute draft team query");
	$draft_team = pg_fetch_array($team, NULL, PGSQL_ASSOC);
	
	// Display the login form
	include_once('_SNIPPETS/head.php');
?>
<div class="container">
	<div class="jumbotron">
		<?php
			echo '<h1>Draft</h1><br>';
			echo '<small>Round '.$_SESSION['draft'].'</small><br>';
			echo $draft_team['name'];
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