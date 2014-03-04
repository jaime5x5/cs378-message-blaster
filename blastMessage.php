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

			if ($a['use_email'] == "1"){
				$_SESSION['mail_count'] = $_SESSION['mail_count'] + 1;
				$customer_email = $a['customer_email'];
				$customer_name = $a['customer_name'];
				sendMail($company_id, $message_content, $customer_name, $customer_email);
			}
			if ($a['use_phone'] == "1"){
				$_SESSION['sms_count'] = $_SESSION['sms_count'] + 1;
				$customer_phone = $a['customer_phone'];
				$customer_name = $a['customer_name'];
				sendtext($company_id, $message_content, $customer_name, $customer_phone);
			}
				
		endforeach;

		header("Location: viewMessageLog.php");
	}

?>
<html>
	<head>
		<title>Blast</title>
		<script type="text/javascript" src="//widget.textmarks.com/widget/tm/41411/JDWCS378/sub2/widget.js?id=tm_widget_sub_1393877939044"></script>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Blast Message</h1><br />
		<hr>
		<a href="landing.php">Home</a>
		<a href="logout.php">Logout</a>
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
		<!-- 41411/JDWCS378 TextMarks Subscription Widget -->
<!-- To re-configure, visit www.TextMarks.com/manage/keywords/manage/MY_KEYWORD/promote/widget_sub2/ -->
<div id="tm_widget_sub_1393889598269" data-h1="SMS alerts" data-h2="Up-to-date info sent to your phone" data-f1="" data-in_name="0" data-in_email="0" data-in_cust="0" data-in_custnam="Custom" data-width="250" data-brd="3" data-frm_brd="1" data-bdy_hmarg="10" data-bdy_vspc="10" data-frm_vspc="10" data-frm_elh="30" data-h1_fsz="29" data-h2_fsz="14" data-f1_fsz="14" data-inp_fsz="14" data-btn_fsz="16" data-font="Trebuchet MS" data-caps="1" data-box_shd="1" data-crn_rnd="25" data-btn_crn_rnd="5" data-h1_shd_ofs="2" data-h1_shd_blur="3" data-bg_c="#e1e1e1" data-bg_grad_c="#fafafa" data-brd_c="#999999" data-h1_c="#999999" data-h1_shd_c="#eeeeee" data-h2_c="#666666" data-f1_c="#666666" data-disc_c="#999999" data-cdp_c="#f7f7f7" data-pow_c="#999999" data-frm_c="#999999" data-frm_brd_c="#666666" data-inp_bg_c="#f7f7f7" data-inp_c="#333333" data-res_c="#eeeeee" data-plc_c="#666666" data-btn_bg_c="#f4f5f5" data-btn_bg_grad_c="#dfdddd" data-btn_brd_c="#d7dada" data-btn_lbl_c="#666666" data-btn_dis_c="#909090" data-btn_dis_lbl_c="#777777" data-btn_h_c="#d9dddd" data-btn_h_grad_c="#c6c3c3" data-btn_h_brd_c="#bfc4c4" data-btn_h_lbl_c="#666666"><div class="tm_widget_pow" style="font-family:'Trebuchet MS',arial,sans-serif;font-size:12px;text-align:center;color:#999;margin:3px auto;"><a style="color:#999;text-decoration:none;" href="http://www.TextMarks.com/?affid=sub2_widget">Text messaging powered by <span>TextMarks.com</span></a></div></div><script type="text/javascript" src="//widget.textmarks.com/widget/tm/41411/JDWCS378/sub2/widget.js?id=tm_widget_sub_1393889598269"></script>
	</body>
</html>