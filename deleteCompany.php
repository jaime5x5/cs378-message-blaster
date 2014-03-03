<?php
	session_start();

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];

	require('model.php');

	if (isset($_POST['delcomp'])) { 
		
		$db = getDatabase();

		$isOwner = verifyOwnershipCompany($company_id, $db);

		if (!$isOwner)
    		die('Error, Insufficent priviledge.');
		else
			deleteCompany($db, $company_id);

		$db->close();
		header("Location:logout.php");
	}


?>
<html>
	<head>	
		<title>Delete Company</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">		
	</head>
	<body>
		<a href=logout.php?ref="<?php echo $urle ?>">Logout</a>
		<h1>Delete Company</h1><br />
		<hr>
		<div name="memberdiv">
			<form action = "#" method="post" onsubmit="return confirm('Your account will now be deleted. Are you sure?')">
				<div>    
					<label for="company_name">Company Name:</label>
					<input type="text" name="company_name" id="company_name" placeholder="Acme Super Genius Kits" value="<?php echo $company_name ?>" readonly />  
				</div>
				<div>
			        <input type="hidden" value="company_id" />
			        <input type="submit" name="delcomp" id="delcomp" value="Confirm" />
			    </div>
			    <a href=landing.php?ref="<?php echo $urle ?>">Cancel</a>
			</form>
		</div>
	</body>
</html>