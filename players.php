<?php
	// First, make sure we are on HTTPS, if not, switch to that
	if (!$_SERVER['HTTPS']) header('location: https://babbage.cs.missouri.edu/~cs3380f14grp10');
	
	// Second, make sure we are logged in, if not, redirect to index.php
	session_start();
	$logged_in = empty($_SESSION['login']) ? false : $_SESSION['login'];
	if (!$logged_in) header('location: index.php');
	
	// Display the login form
	include_once('_SNIPPETS/head.php');
?>
<div class="container">
	<div class="page-header">
		<h1>2007 Stats</h1>
    </div>
		
		<!--dropdown which calls the function sending value of users choice-->
		<form id="choice" style="float: left">
			<select class="form-control" name="users" onchange="showUser(this.value)">
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