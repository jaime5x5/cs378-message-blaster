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
	if( isset($_GET["filter"]) && ($_GET["filter"] == "e" || $_GET["filter"] == "u") )
			$filter = $_GET["filter"];

	$pageNum = 1;
	if(isset($_GET["page"]))
		$pageNum = intval($_GET["page"]);

	$db = getDatabase();

	$customerCount = getPageCount($company_id, $filter, $db);

	$pageCount = intval(ceil($customerCount / PAGESIZE));

	$pageNum = min(max($pageNum, 1), $pageCount);

	$results = getCustomers($company_id, $pageNum, PAGESIZE, $filter, $db);

	$db->close();

	$urle = urlencode("test.php?filter=$filter&page=$pageNum");
?>
<html>
	<head>
		<title><?php echo $company_name?> - Message Blaster</title>
	</head>
	<body>
		<h1>Welcome <?php echo $company_name?> - Message Blaster</h1><br />
		<hr>
		<a href="<?php echo "view.php?filter=e" ?>">Customers email</a>&nbsp;&nbsp;
		<a href="<?php echo "view.php?filter=t" ?>">Customers text</a>&nbsp;&nbsp;
		<a href="<?php echo "view.php?filter=a" ?>">View All</a>&nbsp;&nbsp;
		<a href="logout.php" id="logout-button">Logout</a>
		<br /><br />
		<?php
			if($customerCount > 0):
			foreach ($results as $i => $a) :
		?>
	
				<form action="#">
				<fieldset>
					<legend>Company</legend>
					<div>"    
						<label for="company_name">Company Name:</label>
						<input type="text" name="company_name" id="company_name" placeholder="Acme Super Genius Kits" value="<?php echo $a['company_name'] ?>"/>  
					</div>
					<div>
						<input type="hidden" value="company_id" />
						<!-- only on admin -->
						<input type="submit" value="Add" />
						<!-- only on admin -->
						<input type="submit" value="Edit" />
						<!-- only on admin -->
						<input type="submit" value="Delete" />
					</div>
				</fieldset>
		<?php
			endforeach;
		?>
			page <?php echo $pageNum ?> of <?php echo $pageCount ?> <br />
		<?php	
				if($pageCount > 1):
				?>
				<a href="<?php echo "view.php?filter=$filter&page=1" ?>">First</a>&nbsp;&nbsp;<a href="<?php echo "view.php?filter=$filter&page=".($pageNum-1) ?>">Prev</a>&nbsp;&nbsp;
				<?php
				for ($i=1; $i <= $pageCount ; $i++) { 
					echo "<a href=\"view.php?filter=$filter&page=$i\">$i</a>&nbsp;&nbsp;";
				}
				?>
				<a href="<?php echo "view.php?filter=$filter&page=".($pageNum+1) ?>">Next</a>&nbsp;&nbsp;<a href="<?php echo "view.php?filter=$filter&page=$pageCount" ?>">Last</a>&nbsp;&nbsp;
				<?php
				endif;
			else:
		?>
			No customers here.
		<?php endif ?>
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
		        <input type="submit" value="Add" />
		        <input type="submit" value="Edit" />  
		        <input type="submit" value="Delete" />      
		    </div>

		  </fieldset>
		  <fieldset>
		    <legend>Customer</legend>
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
		        <input type="submit" value="Edit" />  
		        <input type="submit" value="Delete" />
		    </div>
		  </fieldset>

		</form>  
	</body>
</html>