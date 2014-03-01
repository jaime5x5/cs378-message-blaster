<?php
	session_start();

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];
?>
<html>
	<head>
		<title>Add Customer</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Add Customer</h1><br />
		<hr>
		<a href=logout.php?ref="<?php echo $urle ?>">Logout</a>
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
			        <input type="checkbox" name="use_email" id="use_email" />
			     </div>
			    
			     <div>
			        <label for="customer_phone">Phone:</label>
			        <input type="text" name="customer_phone" id="customer_phone" placeholder="1-509-867-5309"  value=""/>      
			        <label for="use_phone">Use Phone:</label>
			        <input type="checkbox" name="use_phone" id="use_phone" />
			     </div>
			    
			    <div>
			        <input type="hidden" value="customer_id" />
			        <input type="submit" value="Add" />
							        
			    </div>
				<a href=landing.php?ref="<?php echo $urle ?>">Cancel</a>
			    
			</fieldset>
		</form>
	</body>
</html>