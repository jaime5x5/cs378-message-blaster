<?php
	session_start();

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];
	$sms_count = 0;
	$_SESSION['sms_count'] = $sms_count;

	if(!isset($_SESSION['company_id'])) {
		header("Location: login.php");
		die();
	}

	require_once 'model.php';

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$filter = "a"; 
		$pagesize = 10;
		$message_content = test_input($_POST['message_content']);
		$pageNum = 1;
		$db = getDatabase();
		$customerCount = 1;
		$pageCount = intval(ceil($customerCount / $pagesize));
		$pageNum = min(max($pageNum, 1), $pageCount);
		$results = getCustomers($company_id, 1, $pagesize, $filter, $db);
		$db->close();

		foreach ($results as $i => $a) :
		//Send an SMS to a phone number.		
			if ($a['use_phone'] == "1"){
				$_SESSION['sms_count'] = $_SESSION['sms_count'] + 1;
				$customer_phone = $a['customer_phone'];
				$sendText = sendtext($company_id, $customer_phone, $message_content, $_SESSION['sms_count']);

			}
				
		endforeach;			

	}

?>
<html>
	<head>
		<title>Blast</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Blast Message</h1><br />
		<hr>
		<a href=landing.php?ref="<?php echo $urle ?>">Home</a>
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