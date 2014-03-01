<?php
	session_start();

	if(!isset($_SESSION['company_id']))
	{
		header("Location: login.php");
		die();
	}
	require_once 'model.php';

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];

	const PAGESIZE = 10; //just hard code :)

	$filter = "a"; //[e]mail, [t]ext, [a]ll
	if( isset($_GET["filter"]) && ($_GET["filter"] == "e" || $_GET["filter"] == "t") )
			$filter = $_GET["filter"];

	$pageNum = 1;
	if(isset($_GET["page"]))
		$pageNum = intval($_GET["page"]);

	$db = getDatabase();

	$customerCount = 1;

	$pageCount = intval(ceil($customerCount / PAGESIZE));

	$pageNum = min(max($pageNum, 1), $pageCount);

	$results = getCustomers($company_id, 1, PAGESIZE, $filter, $db);

	$db->close();

	$urle = urlencode("landing.php?filter=$filter&page=$pageNum");
?>
<html>
	<head>
		<title>Message Blaster</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1><?php echo strtoupper($company_name) ?> Welcome to Message Blaster</h1><br />
		<hr>
		<a href=blastMessage.php?ref="<?php echo $urle ?>">Blast a Message</a>
		<a href=viewMessageLog.php?ref="<?php echo $urle ?>">View Message Log</a>
		<a href=logout.php?ref="<?php echo $urle ?>">Logout</a>
		<br /><br />
		<?php
			if($customerCount > 0):
		?>
		<form action="#">
		<fieldset>
			<legend>Company</legend>
			<div>    
				<label for="company_name">Company Name:</label>
				<input type="text" name="company_name" id="company_name" placeholder="Acme Super Genius Kits" value="<?php echo $company_name ?>" readonly />  
			</div>
			<div>
		<?php 
			if ($company_name == 'brian' || $company_name == 'stephen' ||  $company_name == 'jaime'){
				$is_admin = $_SESSION['company_name'];
				print "<a href=addCompany.php?ref=" . $urle . ">Add Company</a>&nbsp;&nbsp";
			}
		?>
				<a href=editCompany.php?ref="<?php echo $urle ?>">Edit Company</a>&nbsp;&nbsp;
				<a href=deleteCompany.php?ref="<?php echo $urle ?>">Delete Company</a>&nbsp;&nbsp;
			</div>
		</fieldset>


		<fieldset>
			<legend>Customers</legend>
			<a href="<?php echo "addCustomer.php" ?>">Add New Customer</a>&nbsp;&nbsp;
			<h6>View customer list by:</h6>
			<a href="<?php echo "landing.php?filter=e" ?>">Customers email</a>&nbsp;&nbsp;
			<a href="<?php echo "landing.php?filter=t" ?>">Customers text</a>&nbsp;&nbsp;
			<a href="<?php echo "landing.php?filter=a" ?>">View Both</a>&nbsp;&nbsp;
			
		</fieldset>

		<?php

			foreach ($results as $i => $a) :
				if ($a['use_email'] == 1)
					$email_checked = "checked";
				else 
					$email_checked = "";	
				if ($a['use_phone'] == 1)
					$phone_checked = "checked";
				else 
					$phone_checked = "";	
		?>
	

		<fieldset>
		    <legend>Customer</legend>
		     <div>
		        <label for="customer_name">Name: </label>
		        <input type="text" name="customer_name" id="customer_name" placeholder="John Smith" value="<?php echo $a['customer_name']; ?> " readonly />
		     </div>
		    
		     <div>
		        <label for="customer_email">Email:</label>
		        <input type="text" name="customer_email" id="customer_email" placeholder="jsmith@acme.com" value="<?php echo $a['customer_email']; ?> " readonly />     
		        <label for="use_email">Use Email:</label>
		        <input type="checkbox" name="use_email" id="use_email"  <?php echo $email_checked; ?> />
		     </div>
		    
		     <div>
		        <label for="customer_phone">Phone:</label>
		        <input type="text" name="customer_phone" id="customer_phone" placeholder="5098675309"  value="<?php echo $a['customer_phone']; ?>" readonly/>      
		        <label for="use_phone">Use Phone:</label>
		        <input type="checkbox" name="use_phone" id="use_phone"  <?php echo $phone_checked; ?>  />
		     </div>
		    
		    <div>
				<a href=editCustomer.php?ref="<?php echo $urle ?>">Edit Customer</a>&nbsp;&nbsp;
				<a href=deleteCustomer.php?ref="<?php echo $urle ?>">Delete Customer</a>&nbsp;&nbsp;
		    </div>
		  </fieldset>

		<?php
			endforeach;
		?>

		<?php endif ?>

	</body>
</html>