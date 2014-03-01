<?php
	session_start();

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];

	require_once 'model.php';
	
	if (isset($_POST['company_name'])){
		// var_dump($_POST);
		if(test_input($_POST['company_name']) != '' && test_input($_POST['pwd']) != '' ) {		
			$db = getDatabase();
			
			$query = $db->prepare("INSERT INTO companies SET company_name=?, pwd=? ");
		
			if (!$query)
	    		die('Error, Could not update database.');
		
			$query->bind_param("ss", $_POST['company_name'], $_POST['pwd']);
			
			$query->execute();
			
			$db->close();
			
		}	
	header("Location: ". (isset($_GET['ref']) ? urldecode($_GET['ref']) : "landing.php"));
	die("done");
	}
?>
<html>
	<head>	
		<title>Add Company</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">		
	</head>
	<body>
		<a href=logout.php?ref="<?php echo $urle ?>">Logout</a>
		<h1>Add Company</h1><br />
		<hr>
		<div name="memberdiv">
			<form action = "#" method="post">
				<div>    
					<label for="company_name">Company Name:</label>
					<input type="text" name="company_name" id="company_name" placeholder="Acme Super Genius Kits" value="" />  
				</div>
				<div>    
					<label for="pwd">Password:</label>
					<input type="password" name="pwd" id="pwd" placeholder="password" value="" />  
				</div>
				<div>
			        <input type="submit" value="Add" />
			    </div>
			    <a href=landing.php?ref="<?php echo $urle ?>">Cancel</a>
			</form>
		</div>
	</body>
</html>