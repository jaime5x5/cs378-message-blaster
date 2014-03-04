<?php
	session_start();

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];

	require_once 'model.php';
	
	if (isset($_POST['customer_name'])){
		var_dump($_POST);
		if(test_input($_POST['customer_name']) != '') {	
			//sanitize
			$customer_name = test_input($_POST['customer_name']);
			$customer_email = test_input($_POST['customer_email']);
			$customer_phone = test_input($_POST['customer_phone']);
			//translate
			if(isset($_POST['use_phone']))
				$use_phone = "1";
			else 
				$use_phone = "0";
			if(isset($_POST['use_email']))
				$use_email = "1";
			else 
				$use_email = "0";

			$customer_id = "";

			$db = getDatabase();
			
			$query = $db->prepare("INSERT INTO customers SET company_id=?, customer_name=?, customer_email=?, use_email=?, customer_phone=?, use_phone=? ");
		
			if (!$query)
	    		die('Error, Could not update database.');
			
			$query->bind_param("issisi", $company_id, $customer_name, $customer_email, $use_email, $customer_phone, $use_phone);
			
			$query->execute();
			
			$db->close();
			
		}	
	header("Location: ". (isset($_GET['ref']) ? urldecode($_GET['ref']) : "landing.php"));
	die("done");
	}
?>
<html>
	<head>
		<title>Add Customer</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Add Customer</h1><br />
		<hr>
		<a href="logout.php">Logout</a>
		<form action="#" method="post">
			<fieldset>
			    <legend>Add New Customer</legend>
			     <div>
			        <label for="customer_name">Name: </label>
			        <input type="text" name="customer_name" id="customer_name" placeholder="John Smith" value=""/>
			     </div>
			    
			     <div>
			        <label for="customer_email">Email:</label>
			        <input type="text" name="customer_email" id="customer_email" placeholder="jsmith@acme.com" value="" />     
			        <label for="use_email">Use Email:</label>
			        <input type="checkbox" name="use_email" id="use_email" value=""/>
			     </div>
			    
			     <div>
			        <label for="customer_phone">Phone:</label>
			        <input type="text" name="customer_phone" id="customer_phone" placeholder="1-509-867-5309"  value=""/>      
			        <label for="use_phone">Use Phone:</label>
			        <input type="checkbox" name="use_phone" id="use_phone" value=""/>
			     </div>
			    
			    <div>
			        <input type="hidden" value="customer_id" />
			        <input type="submit" value="Add" />
							        
			    </div>
				<a href="landing.php">Cancel</a>
			    
			</fieldset>
		</form>
	</body>
</html>