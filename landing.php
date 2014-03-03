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

	const PAGESIZE = 5; //just hard code :)

	$filter = "a"; //[e]mail, [t]ext, [a]ll
	if( isset($_GET["filter"]) && ($_GET["filter"] == "e" || $_GET["filter"] == "t") )
			$filter = $_GET["filter"];

	$pageNum = 1;
	if(isset($_GET["page"]))
		$pageNum = intval($_GET["page"]);
		$db = getDatabase();
		$customerCount = getPageCountCustomer($company_id, $db);	
		$pageCount = intval(ceil($customerCount / PAGESIZE));	
		$pageNum = min(max($pageNum, 1), $pageCount);
		$results = getCustomers($company_id, $pageNum, PAGESIZE, $filter, $db);
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

		<form action="#" method="">
			<div>    
				<label for="company_name">Company Name:</label>
				<input type="text" name="company_name" id="company_name" placeholder="Acme Super Genius Kits" value="<?php echo $company_name ?>" readonly />  
			</div>
		<?php 
			if ($company_name == 'brian' || $company_name == 'stephen' ||  $company_name == 'jaime'){
				$is_admin = $_SESSION['company_name'];
				print "<a href=addCompany.php?ref=" . $urle . ">Add Company</a>&nbsp;&nbsp";
			}
		?>
				<a href=editCompany.php?ref="<?php echo $urle ?>">Edit Company</a>&nbsp;&nbsp;
				<a href=deleteCompany.php?ref="<?php echo $urle ?>">Delete Company</a>&nbsp;&nbsp;
			</div>
			<a href="<?php echo "addCustomer.php" ?>">Add New Customer</a>&nbsp;&nbsp;
			
			<h6>View customer list by:</h6>
			<a href="<?php echo "landing.php?filter=e" ?>">Customers email</a>&nbsp;&nbsp;
			<a href="<?php echo "landing.php?filter=t" ?>">Customers text</a>&nbsp;&nbsp;
			<a href="<?php echo "landing.php?filter=a" ?>">View Both</a>&nbsp;&nbsp;
			
		</form>
		<form action="editCustomer.php" method="post">
			<table class="rwd-table">
			     <tr>
			     	<th><label for="customer_name">Name</label></th>
			     	<th><label for="customer_email">Email</label></th>
			     	<th><label for="use_email">Use Email</label></th>
			     	<th><label for="customer_phone">Phone</label></th>
			     	<th><label for="use_phone">Use Phone:</label></th>
			     	<th>Modify</th>			        
			     </tr>
		<?php
			if($customerCount > 0):
		?>
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

				$customer_id = $a['customer_id'];

		?>
			     <tr>
			     	<td data-th="<?php echo $a['customer_name']; ?>"><input type="text" name="customer_name" id="customer_name" placeholder="John Smith" value="<?php echo $a['customer_name']; ?> " readonly /></td>
			     	<td data-th="<?php echo $a['customer_email']; ?>"><input type="text" name="customer_email" id="customer_email" placeholder="jsmith@acme.com" value="<?php echo $a['customer_email']; ?> " readonly /> </td>
			     	<td data-th=""><input type="checkbox" name="use_email" id="use_email"  value="" <?php echo $email_checked; ?> /></td>
			     	<td data-th="<?php echo $a['customer_phone']; ?>"><input type="text" name="customer_phone" id="customer_phone" placeholder="5098675309"  value="<?php echo $a['customer_phone']; ?>" readonly/></td>
			     	<td data-th=""><input type="checkbox" name="use_phone" id="use_phone"  value=""  <?php echo $phone_checked; ?> /></td>
			     	<td data-th="<?php echo $customer_id; ?>"><input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>" />
			        <input type="submit" name="edit_customers" id="edit_customers" value="Edit/Delete" /></td>		        
			     </tr>
   
			    <div>
					
			    </div>

		<?php
			endforeach;
		?>
			</table>
			<p>page <?php echo $pageNum ?> of <?php echo $pageCount ?></p>
		</form>
		<?php	
				if($pageCount >= 1):
				?>
				<a href="<?php echo "landing.php?filter=$filter&page=1" ?>">First</a>&nbsp;&nbsp;<a href="<?php echo "landing.php?filter=$filter&page=".($pageNum-1) ?>">Prev</a>&nbsp;&nbsp;
				<?php
				for ($i=1; $i <= $pageCount ; $i++) { 
					echo "<a href=\"landing.php?filter=$filter&page=$i\">$i</a>&nbsp;&nbsp;";
				}
				?>
				<a href="<?php echo "landing.php?filter=$filter&page=".($pageNum+1) ?>">Next</a>&nbsp;&nbsp;<a href="<?php echo "landing.php?filter=$filter&page=$pageCount" ?>">Last</a>&nbsp;&nbsp;
				<?php
				endif;
			else:
	 	endif ?>

	</body>
</html>