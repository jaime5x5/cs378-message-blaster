<?php
	session_start();

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];
?>
<html>
	<head>
		<title>Delete Customer</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Delete Customer</h1><br />
		<hr>
		<a href=logout.php?ref="<?php echo $urle ?>">Logout</a>
		<form action="#" method="post">
			<fieldset>
			    <legend>Delete Customer</legend>
			     <div>
			        <label for="customer_name">Name: </label>
			        <input type="text" name="customer_name" id="customer_name" placeholder="John Smith" value="" readonly />
			     </div>
			    
			     <div>
			        <label for="customer_email">Email:</label>
			        <input type="text" name="customer_email" id="customer_email" placeholder="jsmith@acme.com" value="" readonly />     
			        <label for="use_email">Use Email:</label>
			     </div>
			    
			     <div>
			        <label for="customer_phone">Phone:</label>
			        <input type="text" name="customer_phone" id="customer_phone" placeholder="1-509-867-5309"  value="" readonly />      
			        <label for="use_phone">Use Phone:</label>
			     </div>
			    
			    <div>
			        <input type="hidden" value="customer_id" />
			        <input type="submit" value="Confirm" />						        
			    </div>
				<a href=landing.php?ref="<?php echo $urle ?>">Cancel</a>
			    
			</fieldset>
		</form>
	</body>
</html>