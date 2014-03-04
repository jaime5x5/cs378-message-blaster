<?php
	session_start();
	// var_dump($_POST);
	// var_dump($_GET);
	require('model.php');

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];

	$urle = urlencode("landing.php");

	if(!isset($_SESSION['company_id'])){
		header("Location: login.php");
		die();
	}

	if (isset($_GET['customer_id']) && !isset($_POST['customer_name'])){
		$customer_id = $_GET['customer_id'];
		$db = getDatabase();
		$results = getCustomer($customer_id, $db);		
		$db->close();
		$customer_name = $results['customer_name'];
		$customer_email = $results['customer_email'];
		$use_email = $results['use_email'];
		$customer_phone = $results['customer_phone'];
		$use_phone = $results['use_phone'];

		// translate for display
		if($use_email == 1)
			$use_email = "checked";
		if($use_phone == 1)
			$use_phone = "checked";			
	} 
	else
	{
			//sanitize
		var_dump($_POST);
		$customer_name = test_input($_POST['customer_name']);
		$customer_email = test_input($_POST['customer_email']);
		$customer_phone = test_input($_POST['customer_phone']);
		$customer_id = test_input($_POST['customer_id']);
		// translate for display
		if(isset($_POST['use_email']))
			$use_email = "checked";
		if(isset($_POST['use_phone']))
			$use_phone = "checked";			

		if (isset($_POST['edit'])){
			// translate for db
			if(isset($_POST['use_phone']))
				$use_phone = "1";
			else 
				$use_phone = "0";
			if(isset($_POST['use_email']))
				$use_email = "1";
			else 
				$use_email = "0";

			$customer_id = test_input($_POST['customer_id']);

			$db = getDatabase();

			$isOwner = verifyOwnership($company_id, $customer_id, $db);

			if (!$isOwner)
	    		die('Error, Insufficent priviledge.');
			else
				updateCustomer($db, $customer_name, $customer_email, $use_email, $customer_phone, $use_phone, $customer_id);
			
			$db->close();
			header("Location:landing.php");
		}
		else if (isset($_POST['delete'])) { 
			$db = getDatabase();

			$isOwner = verifyOwnership($company_id, $customer_id, $db);
			if (!$isOwner)
	    		die('Error, Insufficent priviledge.');
			else
				deleteCustomer($db, $customer_id);
			$db->close();
			header("Location:landing.php");
		}
	}

?>
<html>
	<head>
		<title>Edit/Delete Customer</title>
		<script src="js/valid.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Edit/Delete Customer</h1><br />
		<hr>
		<a href="logout.php">Logout</a>
		<form action="editCustomer.php" method="post" onsubmit="return confirm('Confirm action?')">
			<fieldset>
			    <legend>Edit/Delete Customer</legend>
			     <div>
			        <label for="customer_name">Name: </label>
			        <input type="text" name="customer_name" id="customer_name" placeholder="John Smith" value="<?php echo $customer_name ?>"/>
			     </div>
			    
			     <div>
			        <label for="customer_email">Email:</label>
			        <input type="text" name="customer_email" id="customer_email" placeholder="jsmith@acme.com" value="<?php echo $customer_email ?>" />     
			        <label for="use_email">Use Email:</label>
			        <input type="checkbox" name="use_email" id="use_email" <?php echo $use_email ?> />
			     </div>
			    
			     <div>
			        <label for="customer_phone">Phone:</label>
			        <input type="text" name="customer_phone" id="customer_phone" placeholder="1-509-867-5309"  value="<?php echo $customer_phone ?>"/>      
			        <label for="use_phone">Use Phone:</label>
			        <input type="checkbox" name="use_phone" id="use_phone" <?php echo $use_phone ?> />
			     </div>
			    
			    <div>
			        <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id ?>" />
			        <input type="submit" name="edit" id="edit" value="Update" />
			        <input type="submit" name="delete" id="delete" value="Delete" />
			        						        
			    </div>
				<a href=landing.php>Cancel</a>
			    
			</fieldset>
		</form>
	</body>
</html>