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
	if ($company_name == 'brian' || $company_name == 'stephen' ||  $company_name == 'jaime'){
		$is_admin = $_SESSION['company_name'];
	}
	else {
		$is_admin = NULL;
	}

	$filter = "company_id"; //[e]mail, [t]ext, [a]ll
	if( isset($_GET["filter"]) && ($_GET["filter"] == "message_time" || $_GET["filter"] == "message_content") )
			$filter = $_GET["filter"];

	$pageNum = 1;
	if(isset($_GET["page"]))
		$pageNum = intval($_GET["page"]);

	$db = getDatabase();

	$customerCount = 1;

	$pageCount = intval(ceil($customerCount / PAGESIZE));

	$pageNum = min(max($pageNum, 1), $pageCount);
	if ($is_admin){
		$results = getAllMessages($company_id, 1, PAGESIZE, $filter, $db);
	}
	else {
		$results = getMessages($company_id, 1, PAGESIZE, $filter, $db);
	}
		

	$db->close();

	$urle = urlencode("landing.php?filter=$filter&page=$pageNum");
?>
<html>
	<head>
		<title>Message Blaster Log</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1><?php echo strtoupper($company_name) ?>&#8217;S Message Blaster Log</h1><br />
		<hr>
		<a href=landing.php?ref="<?php echo $urle ?>">Home</a>
		<a href=logout.php?ref="<?php echo $urle ?>">Logout</a>
		<br /><br />
		<?php
			if($customerCount > 0):
		?>
		<h6>View message list by:</h6>
			<a href="<?php echo "viewMessageLog.php?filter=message_time" ?>">Time</a>&nbsp;&nbsp;
			<?php if($is_admin){ ?>
			<a href="<?php echo "viewMessageLog.php?filter=company_id" ?>">Company</a>&nbsp;&nbsp;
			<?php } ?>		
			<a href="<?php echo "viewMessageLog.php?filter=message_content" ?>">Message Content</a>&nbsp;&nbsp;
		<table class="rwd-table">
		  <tr>
		    <th>Message Time</th>
		    <th>Message Content</th>
		    <th>Company ID</th>
		  </tr>
		<?php

			foreach ($results as $i => $a) :

		?>
		  <tr>
		    <td data-th="message_time"><?php echo $a['message_time'] ?></td>
		    <td data-th="message_content"><?php echo $a['message_content'] ?></td>
		    <td data-th="company_id"><?php echo $a['company_id'] ?></td>
		  </tr>

		<?php
			endforeach;
		?>
		</table>
		<?php endif ?>

	</body>
</html>