<?php
	session_start();

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];
	$sms_count = 0;
	$mail_count = 0;
	$_SESSION['sms_count'] = $sms_count;
	$_SESSION['mail_count'] = $mail_count;

	if(!isset($_SESSION['company_id'])) {
		header("Location: login.php");
		die();
	}

	require('model.php');

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
			if ($a['use_phone'] == "1" && $a['use_email'] == "1"){
				$_SESSION['sms_count'] = $_SESSION['sms_count'] + 1;
				$customer_phone = $a['customer_phone'];
				$customer_name = $a['customer_name'];
				sendtext($company_id, $message_content, $customer_name, $customer_phone);
			}
			if ($a['use_email'] == "1"){
				$_SESSION['mail_count'] = $_SESSION['mail_count'] + 1;
				$customer_email = $a['customer_email'];
				$customer_name = $a['customer_name'];
				sendMail($company_id, $message_content, $customer_name, $customer_email);
			}
				
		endforeach;			
		header("Location: viewMessageLog.php");
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
		<h3>Message to Blast:</h3>
		<form action = "#" method="post" onsubmit="return confirm('Confirm message?')">
		    <div>
		        <input type="submit" name="blast" id="blast" value="Blast" />
		    </div>
		     <div>
		        <label for="message_content">Message Content:</label>
		        <input type="text" name="message_content" id="message_content" placeholder="Buy Acme" value=""/>
		     </div>

		</form>
	</body>
</html>