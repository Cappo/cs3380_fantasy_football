<!DOCTYPE html>
<html>
<head>
	<title>Fantasy Football</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>
		//function for the drop down box
		function showUser(str) {
			if (str=="") {//if nothing chosen then nothing displays
				document.getElementById("txtHint").innerHTML="";
				return;
			} 
			if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			} else { // code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
				}
			}
			//this will pass the value q(value of dropdown box) to user.php
			xmlhttp.open("GET","user.php?q="+str,true);
			xmlhttp.send();
		}
		//search box function is same as dropdown function
		function showResult(str) {
			if (str.length==0) { 
				document.getElementById("livesearch").innerHTML="";
				document.getElementById("livesearch").style.border="0px";
				return;
			}
			if (window.XMLHttpRequest) {
				xmlhttp=new XMLHttpRequest();
			} else {  // code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
				}
			}
			//sends z over to search.php
			xmlhttp.open("GET","search.php?z="+str,true);
			xmlhttp.send();
		}
		//search box function is same as dropdown function
		function showDraft(str) {
			if (str.length==0) { 
				document.getElementById("livesearch").innerHTML="";
				document.getElementById("livesearch").style.border="0px";
				return;
			}
			if (window.XMLHttpRequest) {
				xmlhttp=new XMLHttpRequest();
			} else {  // code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
				}
			}
			//sends z over to search.php
			xmlhttp.open("GET","draft_table.php?z="+str,true);
			xmlhttp.send();
		}
	</script>
<!--    <style>
		#header{
			text-align: center;
			text-decoration: underline;
		}
		#choice{
			text-align: center;
		}
		.nav-pills > li.active > a {
			background-color:gray;
		}
		.nav-pills>li.active>a:hover {
			background-color:gray;
		}
		a{
			color: white;
		}
		body {
			background-color: #E6E6E6;
		}
	</style> -->
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