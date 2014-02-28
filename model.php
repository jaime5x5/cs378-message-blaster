<?php

require_once 'locked/security.php';

//reworked from comments in http://www.php.net/manual/en/mysqli-stmt.fetch.php
//I'm using this to replace the get_results method.

function stmt_get_assoc (&$stmt) {

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
		return " AND email = 1 ";
	
	if($filter == "t")
		return " AND text = 1 ";
	
	return "";
}

function getDatabase()
{
	return new mysqli(__db__host, __db__user, __db__pass, __db__database);
	
	if ($mysqli->connect_error)
    	die('Error, Could not connect to database.');
}

function getPageCount($company_id, $filter, $db)
{
	$query = $db->prepare("SELECT COUNT(customer_id) AS count FROM customers WHERE customer_id = ?" . getFilterQuery($filter));
	
	if (!$query)
    	die('Error, Could not query database.');
	
	$query->bind_param("i", $customer_id);
	$query->execute();
	
	$r = stmt_get_assoc($query);
	
	$count = $r[0]["count"];
	
	$query->close();
	
	return $count;	
}

function getCustomers($company_id, $pageNum, $pageSize, $filter, $db)
{
	$query = $db->prepare("SELECT * FROM customers WHERE company_id = ? ".getFilterQuery($filter)." ORDER BY customer_name LIMIT ?, ?");
	
	if (!$query)
    	die('Error, Could not query database.');
	
	$start = ($pageNum-1)*$pageSize;
	$end = $pageNum*$pageSize;
	
	$query->bind_param("iii", $company_id, $start, $end);
	
	$query->execute();
	
	return stmt_get_assoc($query);
}

// function verifyOwnership($company_id, $mid, $db)
// {
// 	$query = $db->prepare("SELECT COUNT(mid) AS count FROM movies WHERE mid = ? AND uid = ?");
	
// 	if (!$query)
//     	die('Error, Could not query database.');

// 	$query->bind_param("ii", $mid, $uid);
	
// 	$query->execute();
	
// 	$r = stmt_get_assoc($query);
	
// 	$res = ($r[0]['count'] == 1)?TRUE:FALSE;
	
// 	$query->close();
	
// 	return $res;	
// }

// function getMovie($mid, $db)
// {
// 	$query = $db->prepare("SELECT * FROM movies WHERE mid = ?");
	
// 	if (!$query)
//     	die('Error, Could not query database.');
	
// 	$query->bind_param("i", $mid);
	
// 	$query->execute();
	
// 	$r = stmt_get_assoc($query);
	
// 	$query->close();
	
// 	return $r[0];
// }	

// function deleteMovie($db, $mid)
// {
// 	$query = $db->prepare("DELETE FROM movies WHERE mid = ?");
	
// 	if (!$query)
//     	die('Error, Could not query database.');
	
// 	$query->bind_param("i", $mid);
	
// 	$query->execute();
// 	$query->close();
// }

// function setWatched($db, $mid, $date)
// {
// 	$query = $db->prepare("UPDATE movies SET watched = 1, watched_date = ? WHERE mid = ?");
	
// 	if (!$query)
//     	die('Error, Could not query database.');
	
// 	$query->bind_param("si", $date, $mid);
	
// 	$query->execute();
// 	$query->close();
// }

// function setUnwatched($db, $mid)
// {
// 	$query = $db->prepare("UPDATE movies SET watched = 0, watched_date = NULL WHERE mid = ?");
	
// 	if (!$query)
//     	die('Error, Could not query database.');
	
// 	$query->bind_param("i", $mid);
	
// 	$query->execute();
// 	$query->close();
// }

// function getSub($mid, $sub,  $db)
// {
// 	$query = $db->prepare("SELECT * FROM $sub WHERE mid = ?");
	
// 	if (!$query)
//     	die('Error, Could not query database.');
	
// 	$query->bind_param("i", $mid);
	
// 	$query->execute();
	
// 	return stmt_get_assoc($query);
// }

// function getSingleSub($mid, $sub, $idName, $id, $db)
// {
// 	$query = $db->prepare("SELECT * FROM $sub WHERE mid = ? AND $idName = ?");
	
// 	if (!$query)
//     	die('Error, Could not query database.');
	
// 	$query->bind_param("ii", $mid, $id);
	
// 	$query->execute();
	
// 	$res = stmt_get_assoc($query);
	
// 	return $res[0];
// }


?>