<?php
	session_start();

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];

	require('model.php');

	if (isset($_POST['updateCo'])){
		// var_dump($_POST);
		$company_name = test_input($_POST['company_name']);
		$pwd = test_input($_POST['pwd']);

		$db = getDatabase();
		
		$query = $db->prepare("UPDATE companies SET company_name=?, pwd=? WHERE company_id=?");
	
		if (!$query)
    		die('Error, Could not update database.');
	
		$query->bind_param("ssi", $company_name, $pwd, $company_id );
		
		$query->execute();
		
		$db->close();

		$_SESSION['company_name'] = $company_name;	
		header("Location: landing.php");
	}
?>
<html>
	<head>	
		<title>Edit Company</title>
		<script src="js/valid.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="css/style.css">		
	</head>
	<body>
		<a href=logout.php?ref="<?php echo $urle ?>">Logout</a>
		<h1>Edit Company</h1><br />
		<hr>
		<div name="memberdiv">
			<form action = "#" method="post" onsubmit="return confirm('Confirm action?')">
				<div>    
					<label for="company_name">Company Name:</label>
					<input type="text" name="company_name" id="company_name" placeholder="Acme Super Genius Kits" value="<?php echo $company_name ?>"  />  
				</div>
				<div>    
					<label for="pwd">Password:</label>
					<input type="password" name="pwd" id="pwd" placeholder="password" value="" />  
				</div>
				<div>
			        <input type="submit" name="updateCo" id="updateco" value="Update" />
			    </div>
			    <a href=landing.php?ref="<?php echo $urle ?>">Cancel</a>
			</form>
		</div>
	</body>
</html>