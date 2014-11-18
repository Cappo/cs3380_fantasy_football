<!DOCTYPE html>
<html>
<head>
	<title>Fantasy Football</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<head/>
<body>
<?php if ($logged_in){ ?>
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">Fantasy Football</a>
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="nav-collapse">
			<ul class="nav navbar-nav">
				<li><a href="home.php">League</a></li>
				<li><a href="players.php">Players</a></li>
			</ul>
        </div>
	</div>
</nav>
<?php 
}
?>