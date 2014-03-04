<?php

require_once 'locked/security.php';
require('TextMarksV2APIClient.php');

//reworked from comments in http://www.php.net/manual/en/mysqli-stmt.fetch.php
//I'm using this to replace the get_results method.

function stmt_get_assoc(&$stmt) {

	$stmt->store_result();
	$meta = $stmt->result_metadata();
	$out = array();
	$names = array();
	
	while ($column = $meta->fetch_field()) {
   		$bindVarsArray[] = &$results[$column->name];
		$names[] = $column->name;
	}        
	call_user_func_array(array($stmt, 'bind_result'), $bindVarsArray);
	
	// loop through all result rows
	while ($stmt->fetch()) {

    	for( $i = 0; $i < sizeof($names); $i++ )
    	{
        	$row_tmb[ $names[$i] ] = $bindVarsArray[$i];
    	} 
    	$out[] = $row_tmb;
	}
	
	return $out;
}

function getFilterQuery($filter)
{
	if($filter == "e")
		return " AND use_email = 1 ";
	
	if($filter == "t")
		return " AND use_phone = 1 ";

	if($filter == "message_time")
		return " message_time ";
	
	if($filter == "company_id")
		return " company_id ";

	if($filter == "message_content")
		return " message_content ";
	
	return "";
}

function getDatabase()
{
	return new mysqli(__db__host, __db__user, __db__pass, __db__database);
	
	if ($mysqli->connect_error)
    	die('Error, Could not connect to database.');
}

function getAllCountMessage($company_id, $db)
{

	$query = $db->prepare("SELECT COUNT(message_id) AS count FROM messages ");
	
	if (!$query)
    	die('Error, Could not query database.');

	$query->execute();	
	$r = stmt_get_assoc($query);	
	$count = $r[0]['count'];	
	$query->close();
	
	return $count;	
}

function getCountMessage($company_id, $db)
{

	$query = $db->prepare("SELECT COUNT(message_id) AS count FROM messages WHERE company_id = ? ");
	
	if (!$query)
    	die('Error, Could not query database.');

	$query->bind_param("i", $company_id);
	$query->execute();	
	$r = stmt_get_assoc($query);	
	$count = $r[0]['count'];	
	$query->close();
	
	return $count;	
}

function getPageCountCustomer($company_id, $db)
{
	$query = $db->prepare("SELECT COUNT(customer_id) AS count FROM customers WHERE company_id = ?" );
	
	if (!$query)
    	die('Error, Could not query database.');
	
	$query->bind_param("i", $company_id);
	$query->execute();
	$r = stmt_get_assoc($query);	
	$count = $r[0]['count'];
	$query->close();
	
	return $count;	
}

function getCustomers($company_id, $pageNum, $pageSize, $filter, $db)
{
	$query = $db->prepare("SELECT * FROM customers WHERE company_id = ? ".getFilterQuery($filter)." ORDER BY customer_id LIMIT ?, ?");
	
	if (!$query)
    	die('Error, Could not query database.');
	
	$start = ($pageNum-1)*$pageSize;
	$end = $pageNum*$pageSize;
	
	$query->bind_param("iii", $company_id, $start, $end);
	
	$query->execute();

	// var_dump( stmt_get_assoc($query));
	
	return stmt_get_assoc($query);
}

function verifyOwnership($company_id, $customer_id, $db)
{
	$query = $db->prepare("SELECT COUNT(company_id) AS count FROM customers WHERE  company_id= ? AND  customer_id= ?");
	
	if (!$query)
    	die('Error, Could not query database.');

	$query->bind_param("ii", $company_id, $customer_id);	
	$query->execute();	
	$r = stmt_get_assoc($query);
	$res = ($r[0]['count'] == 1)?TRUE:FALSE;	
	$query->close();
	
	return $res;	
}

function deleteCompany($db, $company_id)
{
	$query = $db->prepare("DELETE FROM companies WHERE company_id = ? ");
	
	if (!$query)
    	die('Error, Could not query database.');
	
	$query->bind_param("i", $company_id); 
	$query->execute(); 
	$query->close();
}

function verifyOwnershipCompany($company_id, $db)
{
	$query = $db->prepare("SELECT COUNT(company_id) AS count FROM companies WHERE  company_id= ? ");	
	
	if (!$query)
    	die('Error, Could not query the database.');

	$query->bind_param("i", $company_id);	
	$query->execute();	
	$r = stmt_get_assoc($query);	
	$res = ($r[0]['count'] == 1)?TRUE:FALSE;	
	$query->close();
	
	return $res;	
}

function getCustomer($customer_id, $db)
{
	$query = $db->prepare("SELECT * FROM customers WHERE customer_id = ?");
	
	if (!$query)
    	die('Error, Could not query database.');
	
	$query->bind_param("i", $customer_id);
	
	$query->execute();
	
	$r = stmt_get_assoc($query);
	
	$query->close();
	
	return $r[0];
}	

function deleteCustomer($db, $customer_id)
{
	$query = $db->prepare("DELETE FROM customers WHERE customer_id = ?");
	
	if (!$query)
    	die('Error, Could not query database.');
	
	$query->bind_param("i", $customer_id); 
	$query->execute(); 
	$query->close();
}

function updateCustomer($db, $customer_name, $customer_email, $use_email, $customer_phone, $use_phone, $customer_id){

	$query = $db->prepare("UPDATE customers SET customer_name=?, customer_email=?, use_email=?, customer_phone=?, use_phone= ? WHERE customer_id=?");

	if (!$query)
		die('Error, Could not update database.');
	
	$query->bind_param("ssisii",  $customer_name, $customer_email, $use_email, $customer_phone, $use_phone, $customer_id);
	
	$query->execute();
	$query->close();
}

function getMessages($company_id, $pageNum, $pageSize, $filter, $db)
{
	$query = $db->prepare("SELECT * FROM messages WHERE company_id = ? ORDER BY ".getFilterQuery($filter)."  LIMIT ?, ?");
	
	if (!$query)
    	die('Error, Could not query database.');
	
	$start = ($pageNum-1)*$pageSize;
	$end = $pageNum*$pageSize;
	
	$query->bind_param("iii", $company_id, $start, $end);
	
	$query->execute();
	
	return stmt_get_assoc($query);
}

function getAllMessages($company_id, $pageNum, $pageSize, $filter, $db)
{
	$query = $db->prepare("SELECT * FROM messages ORDER BY".getFilterQuery($filter)." LIMIT ?, ?");
	
	if (!$query)
    	die('Error, Could not query database.');
	
	$start = ($pageNum-1)*$pageSize;
	$end = $pageNum*$pageSize;
	
	$query->bind_param("ii", $start, $end);
	
	$query->execute();
	
	return stmt_get_assoc($query);
}

function sendtext($company_id, $message_content, $rx_by, $medium){

			if($company_id && $message_content && $rx_by && $medium) {
				// $gv = new GoogleVoice(__gv__email, __gv__pwd);
				// $gv->sendSMS($medium, $message_content);
				try
				{
					// Broadcast a message to a TextMark group:
					$sMyApiKey        = 'dev4dollars_com__1a1f9376';
					$sMyTextMarksUser = 'jaime5x5'; // (or my TextMarks phone#)
					$sMyTextMarksPass = 'txrx5x5';
					$sKeyword         = 'JDWCS378';
					$sMessage         = $message_content;
					$tmapi = new TextMarksV2APIClient($sMyApiKey, $sMyTextMarksUser, $sMyTextMarksPass);
					$resp = $tmapi->call('GroupLeader', 'broadcast_message', array(
						'tm' => $sKeyword,
						'msg' => $sMessage
						));
				}
				catch (Exception $e)
				{
					echo "Whoops... Exception caught!\n";
					echo "Error code: " . $e->getCode() . "\n";
					echo "Exception: " . $e . "\n";
				}
				
				$db = getDatabase();		
				$query = $db->prepare("INSERT INTO messages SET  message_time=?, message_content=?, company_id=?, rx_by=?, medium=? ");

				if (!$query)
	    			die('Error, Could not update database.');
	    		$timestamp = NULL;			
				$query->bind_param("ssiss", $timestamp, $message_content, $company_id, $rx_by, $medium);				
				$query->execute();				
				$db->close();
		}
}


function sendMail($company_id, $message_content, $rx_by, $medium){
			if($company_id && $message_content && $rx_by && $medium) {
				// In case any of our lines are larger than 70 characters, we should use wordwrap()
				$message_content = wordwrap($message_content, 70, "\r\n");
				// Send
				// mail($medium, 'Great News', $message_content);
				// log event
				$db = getDatabase();		
				$query = $db->prepare("INSERT INTO messages SET  message_time=?, message_content=?, company_id=?, rx_by=?, medium=? ");

				if (!$query)
	    			die('Error, Could not update database.');
	    		$timestamp = NULL;			
				$query->bind_param("ssiss", $timestamp, $message_content, $company_id, $rx_by, $medium);				
				$query->execute();				
				$db->close();
		}
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

?>