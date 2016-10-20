<?php 

    // First we execute our common code to connection to the database and start the session 
    require("../common.php"); 
     
    // At the top of the page we check to see whether the user is logged in or not 
    if(empty($_SESSION['user'])) 
    { 
        // If they are not, we redirect them to the login page. 
        header("Location: ../login.php"); 
         
        // Remember that this die statement is absolutely critical.  Without it, 
        // people can view your members-only content without logging in. 
        die("Redirecting to login.php"); 
    } 
     
    // Everything below this point in the file is secured by the login system 
     
    // We can display the user's username to them by reading it from the session array.  Remember that because 
    // a username is user submitted content we must use htmlentities on it before displaying it to the user. 
?> 
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	
	<!--<link rel="Shortcut Icon" type="image/ico" href="../images/favicon.ico" />-->
	<meta name="viewport" content="width=device-width, initial-scale=1" />
  
	<title>GGTOURNEYS</title>
  	
  	<!-- Angular __________________________________________ -->
  	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>

	<!-- CSS _____________________________________________-->
	<link href='http://fonts.googleapis.com/css?family=Josefin+Sans:400,600&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="css/icomoon.css" media="screen" />
	<link rel="stylesheet" href="css/magnificpopup.css" media="screen" />
	<link rel="stylesheet" href="style.css" media="screen" />  

	<!-- Fixing Internet Explorer ______________________________________-->
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<link rel="stylesheet" href="css/ie.css" />
	<![endif]-->

</head>
<body class="home fullscreen">
	<!-- Loader _______________________________-->
	<div class="loadreveal"></div>
	<div id="loadscreen"><div id="loader"></div></div>

	<!-- HEADER _____________________________________________-->
	<header role="banner" id="header">
		<fb:login-button autologoutlink="true" scope="user_likes,email"></fb:login-button>
			<!-- Main menu __-->
			<nav id="mainmenu" role="navigation">
			
				<div id="menu-burger"><i class="icon-menu"></i></div>
				<div id="searchicon">
					<i class="icon-search"></i>
					<form action="/" method="get" id="searchbar">
						<fieldset>
							<input type="text" name="s" id="searchbar-input" value="" />
							<button type="submit" id="searchbar-submit"></button>
						</fieldset>
					</form>				
				</div>
				<div id="menu">
					<ul>
						<li><a href="../index.php">Home</a></li>
						<li class="menu-item-has-children"><a href="#">Profile</a>
							<ul class="sub-menu">
								<li><a href="myProfile.html">My Profile</a></li>
								<li><a href="../login.php">Pricing</a></li>
								<li><a href="../register.php">Register</a></li>
								<li><a href="../memberlist.php">Member List</a></li>
								<li><a href="../logout.php">Logout</a></li>
							</ul>
						</li>
						<li class = "current-menu-item"><a href="myTeams.php">My Teams</a></li>
						<!--<li><a href="#">Friends</a></li>
						<li><a href="#">Messages</a></li> -->
						<li><a href="contact.html">Contact Us</a></li> 
					</ul>
				</div>
			</nav>
	</header>

	<section id="content" role="main">
		<div class = "team_list">
			<h1> My Teams </h1>
			<?php
				$username = $_SESSION['user']['username'];
				$userID = $_SESSION['user']['id'];
				
				$query = "
					SELECT 
						Teams.*, users.id, users.username, users.username as Administrator, users.username as Member
					FROM Teams LEFT JOIN users ON users.id = Teams.Admin_ID 
					WHERE Teams.Member_ID = $userID OR Teams.Admin_ID = $userID
					
				";

				try 
			    { 
			        // These two statements run the query against your database table. 
			        $stmt = $db->prepare($query); 
			        $stmt->execute(); 
			    } 
			    catch(PDOException $ex) 
			    { 
			        // Note: On a production website, you should not output $ex->getMessage(). 
			        // It may provide an attacker with helpful information about your code.  
			        die("Failed to run query: " . $ex->getMessage()); 
			    } 

			    // Finally, we can retrieve all of the found rows into an array using fetchAll 
			    $rows = $stmt->fetchAll(); 

			    
			    echo "<table style = 'width: 100%' >";
			    echo "<th> Team Name </th> <th> Administrator </th> <th> Action </th>";
			    foreach ($rows as $row):
			    	//Showing Teams if user is the admin of the Team 
			    	if($userID == $row['Admin_ID'])
			    	{
			    		echo "<tr>
							<td> <a href ='/#'>".$row['Team_Name']."</a> </td>
							<td>".$row['Administrator']."</td>
							<td>
								<form action = '../controller/teamController.php/' method = 'post'>
									<input type = 'hidden' name = 'edit_team' value = 'edit_team'>
									<input type = 'hidden' name = 'team_id' value = ' ". $row['Team_ID']." '>
									<input type = 'submit' value = 'Edit'>
								</form>

								<form action = '../controller/teamController.php/' method = 'post'>
									<input type = 'hidden' name = 'delete_team' value = 'delete_team'>
									<input type = 'hidden' name = 'team_id' value = ' ". $row['Team_ID']." '>
									<input type = 'submit' value = 'Delete'>
								</form>
							</td>
							
			    		</tr>";
			    	}
			    	else
			    	{
				    	echo "<tr> 
							<td> <a href ='/#'>".$row['Team_Name']."</a> </td>
							<td>".$row['Administrator']."</td>
				    	</tr>";
			    	}
			    endforeach;				    
			    echo "</table>";

			?>
		</div>
		<div>
			<br>
			<?php 
			//Creating New Team Message Errors 
			 if(isset($_GET['message'])) {
                	if($_GET['message'] == 1) {
                		echo "Please Enter A Team Name.";
                    } else if($_GET['message'] == 2) {
                    	echo "Please enter the Max Players for your new team.";
                    } else if($_GET['message'] == 3) {
                    	echo "Unfortunately, <strong>".$_SESSION['create_team_name']."</strong> already exist. Please enter a new name.";
                    	//Look into cookies so we can delete the $SESSION[team_name]
                    	unset($_SESSION['create_team_name']);
                    	//var_dump($_SESSION);
                    } else if ($_GET['message'] == 4) {
                    	echo "This email address is already registered";
                    } 
            }
            ?>
        

			<h1> Create New Team </h1>
			<form action = "../controller/teamController.php" method = "post">
				<input type = 'hidden' name = 'create_team' value = 'create_team'>
				<input type = 'hidden' name = 'user_name' value = <?= $username ?> >
				<input type = 'hidden' name = 'user_id' value = <?= $userID ?> >
				<p> 
					Team Name: <input type = "text" name = "team_name"> 
				</p>
				<p>
					Max Players: 
					<select name = "max_players">
						<option value = "--"> -- </option>
						<option value = "1"> 1 </option>
						<option value = "2"> 2 </option>
						<option value = "3"> 3 </option>
						<option value = "4"> 4 </option>
						<option value = "5"> 5 </option>
					</select>
				</p>
				<input type = "submit" value = "Create">
			</form>
			
			
		</div>
		
	</section>
<div class="page_ender">
		<font color="white">
		<!--<h2 class="big no-margin">--><strong>GG Tourneys</strong><!--</h2>-->
		<img src="imgs/LogoMakr.png" alt="Logo" style="width:100px;height:80px;">
		</font>
	</div>
	
	<div font-family="BigNoodleTilting">
		GGTourneys
	</div>

<!-- Javascripts ______________________________________-->
<script src="js/jquery.min.js"></script> 
<script src="js/retina.min.js"></script> 
<!-- include Masonry -->
<script src="js/isotope.pkgd.min.js"></script> 
<!-- include image popups -->
<script src="js/jquery.magnific-popup.min.js"></script> 
<!-- include mousewheel plugins -->
<script src="js/jquery.mousewheel.min.js"></script>
<!-- include carousel plugins -->
<script src="js/jquery.tinycarousel.min.js"></script>
<!-- include svg line drawing plugin -->
<script src="js/jquery.lazylinepainter.min.js"></script>

<!-- Facebook Log in button -->
<script src="facebook_button.js"></script>

<!-- include custom script -->
<script src="js/scripts.js"></script>

</body>
</html>