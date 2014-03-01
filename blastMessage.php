<?php
	session_start();

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];
?>
<html>
	<head>
		<title><?php echo $company_name ?> to Message Blaster</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Blast Message</h1><br />
		<hr>
		<a href=logout.php?ref="<?php echo $urle ?>">Logout</a>
		<form action="#" method="post">
			<fieldset>
			    <legend>Message to Blast</legend>
			    <div>
			        <input type="submit" value="Blast" />
			    </div>
			     <div>
			        <label for="message_content">Message Content:</label>
			        <input type="text" name="message_content" id="message_content" placeholder="Buy Acme" value=""/>
			     </div>
			    <div>
			        <input type="hidden" value="message_id" />     
			    </div>
			</fieldset>
		</form>
	</body>
</html>