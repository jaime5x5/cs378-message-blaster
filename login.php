<?php

require_once 'model.php';

session_start();

if (isset($_POST['submit'])){
	
	$company_name = strtolower(test_input($_POST['company_name']));
	$pwd = strtolower(test_input($_POST['pwd']));
	
	$mysqli = getDatabase();
	
	$query = "SELECT company_id from companies where company_name= ? AND pwd= ?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("ss", $company_name, $pwd);
	$stmt->execute();
	$stmt->bind_result($result);
	$stmt->fetch();
	
	if ($result == "" || $result == NULL){
		echo "Invalid login.  Please try again.";		
	}
	else{
		$_SESSION['company_id'] = $result;
		$_SESSION['company_name'] = $company_name;

		header("Location:landing.php");
	}			
}
?>
<html>
	<head>	
		<title>Welcome to the Message Blaster</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">		
	</head>
	<body>
		<h1>Welcome to Message Blaster</h1>
		<p>Please enter your company name:</p><br/>
		<div name="memberdiv">
			<form action = "login.php" method="post">
				<input type="text" width="400" name="company_name" id="company_name"  value="" ><br/>
				<label>Password:</label><br/>
				<input type="password" width="400" name="pwd" id="pwd"  value="" ><br/><br/>
				<input type="submit" name = "submit" value="Login" style="width: 155;">		
			</form>
		</div>
	</body>
</html>

