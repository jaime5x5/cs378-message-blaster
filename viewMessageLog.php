<?php
	session_start();

	if(!isset($_SESSION['company_id']))
	{
		header("Location: login.php");
		die();
	}
	require('model.php');

	$company_name = $_SESSION['company_name'];
	$company_id = $_SESSION['company_id'];

	const PAGESIZE = 7; //just hard code :)
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

	if ($is_admin){
		$messageCount = getAllCountMessage($company_id, $db);
	}
	else {
		$messageCount = getCountMessage($company_id, $db);
	}

	$pageCount = intval(ceil($messageCount / PAGESIZE));

	$pageNum = min(max($pageNum, 1), $pageCount);

	if ($is_admin){
		$results = getAllMessages($company_id, $pageNum, PAGESIZE, $filter, $db);
	}
	else {
		$results = getMessages($company_id, $pageNum, PAGESIZE, $filter, $db);
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
			if($messageCount > 0):
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
		    <th>Received by</th>
		    <th>Via</th>
		  </tr>
		<?php

			foreach ($results as $i => $a) :

		?>
			  <tr>
			    <td data-th="<?php echo $a['message_time'] ?>"><?php echo $a['message_time'] ?></td>
			    <td data-th="<?php echo $a['message_content'] ?>"><?php echo $a['message_content'] ?></td>
			    <td data-th="<?php echo $a['company_id'] ?>"><?php echo $a['company_id'] ?></td>
			    <td data-th="<?php echo $a['rx_by'] ?>"><?php echo $a['rx_by'] ?></td>
			    <td data-th="<?php echo $a['medium'] ?>"><?php echo $a['medium'] ?></td>
			  </tr>

		<?php
			endforeach;
		?>
		</table>
		<p>page <?php echo $pageNum ?> of <?php echo $pageCount ?></p>
		<?php	
				if($pageCount >= 1):
				?>
				<a href="<?php echo "viewMessageLog.php?filter=$filter&page=1" ?>">First</a>&nbsp;&nbsp;<a href="<?php echo "viewMessageLog.php?filter=$filter&page=".($pageNum-1) ?>">Prev</a>&nbsp;&nbsp;
				<?php
				for ($i=1; $i <= $pageCount ; $i++) { 
					echo "<a href=\"viewMessageLog.php?filter=$filter&page=$i\">$i</a>&nbsp;&nbsp;";
				}
				?>
				<a href="<?php echo "viewMessageLog.php?filter=$filter&page=".($pageNum+1) ?>">Next</a>&nbsp;&nbsp;<a href="<?php echo "viewMessageLog.php?filter=$filter&page=$pageCount" ?>">Last</a>&nbsp;&nbsp;
				<?php
				endif;
			else:
		?>
		<?php endif ?>
	</body>
</html>