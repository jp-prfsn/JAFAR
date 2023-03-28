<?php

	// get original input
	$copy = $_POST["original"];

	//---- GET TITLE ----//
	if (preg_match('/\<title\>(.*?)\<\/title\>/', $copy, $match) == 1) {
	    $campaignCode = $match[1];
	}

	//---- GET BRAND ----//
	//$detectedBrand = substr($campaignCode, 4, 3); 
	$brand = $_POST["clientSelect"];

	//----HSBC----//
	if($brand == "EXP" || $brand == "BMU" || $brand == "MLT"){

		
		//------------------------- General HSBC Rules ----------------------------------------------------------//
		$numberOfSegments = $_POST["numberOfSegments"];

		//-------------------------------- ----- ----------------------------------------------------------//
		//-------------------------------- Expat ----------------------------------------------------------//
		//-------------------------------- ----- ----------------------------------------------------------//
		if($brand == "EXP"){
			echo "Expat";

		}


		//-------------------------------- ------- ---------------------------------------------------------//
		//-------------------------------- Bermuda ---------------------------------------------------------//
		//-------------------------------- ------- ---------------------------------------------------------//
		if($brand == "BMU"){
			echo "Bermuda";
			
		}



		//-------------------------------- ----- ----------------------------------------------------------//
		//-------------------------------- Malta ----------------------------------------------------------//
		//-------------------------------- ----- ----------------------------------------------------------//
		if($brand == "MLT"){
			echo "Malta";
			
		}

	}
	else if($brand == "TP"){


		//-------------------------------- ---------- ----------------------------------------------------------//
		//-------------------------------- TradePoint ----------------------------------------------------------//
		//-------------------------------- ---------- ----------------------------------------------------------//

		
		// replace copy symbol
		$pos1 = stripos($copy, "©");
		if ($pos1 !== false) {
	    	$copy = str_replace("©", "&copy;", $copy);
		}else{
			$copy = '<div style="padding:5px; border:1px solid red; color:#DB0011; margin:5px; font-size:12px; font-weight:bold;"><span style="font-size:16px;">!&nbsp;&nbsp;</span>Copyright Symbol not found</div>' .  $copy;
		}
	

		// PREHEADER
		$pos2 = stripos($copy, "Pre-header goes here");
		if ($pos2 !== false) {
	    	$copy = str_replace('Pre-header goes here', '<%= message.delivery.preHeader %>', $copy);
		}else{
			$copy = '<div style="padding:5px; border:1px solid red; color:#DB0011; margin:5px; font-size:12px; font-weight:bold;"><span style="font-size:16px;">!&nbsp;&nbsp;</span>Preheader 1 not found</div>' .  $copy;
		}
		$pos3 = stripos($copy, "INSERT PRE-HEADER HERE");
		if ($pos3 !== false) {
	    	$copy = str_replace('INSERT PRE-HEADER HERE', '<%= message.delivery.preHeader %>', $copy);
		}else{
			$copy = '<div style="padding:5px; border:1px solid red; color:#DB0011; margin:5px; font-size:12px; font-weight:bold;"><span style="font-size:16px;">!&nbsp;&nbsp;</span>Preheader 2 not found</div>' .  $copy;
		}
	
	
	
	

		// WEBVERSION
		$pos4 = stripos($copy, '<a href="####" target="_blank" data-ed-action="mirror link" style="color:#108096">View online</a>');
		if ($pos4 !== false) {
	    	$copy = str_replace('<a href="####" target="_blank" data-ed-action="mirror link" style="color:#108096">View online</a>', '<a href="<%@ include view=\'tradepoint_mirror_link_only\' %>" target="_blank" data-ed-action="mirror link" style="color:#108096">View online</a>', $copy);
		}else{
			$copy = '<div style="padding:5px; border:1px solid red; color:#DB0011; margin:5px; font-size:12px; font-weight:bold;"><span style="font-size:16px;">!&nbsp;&nbsp;</span>View Online Link (header) not found</div>' .  $copy;
		}

		$pos5 = stripos($copy, '<a class="wr" href="#" style="font-family:\'Kingfisher\', Arial, Helvetica, sans-serif !important; color:#3c3c3c; text-decoration:underline;" target="_blank">View in browser</a>');
		if ($pos5 !== false) {
	    	$copy = str_replace('<a class="wr" href="#" style="font-family:\'Kingfisher\', Arial, Helvetica, sans-serif !important; color:#3c3c3c; text-decoration:underline;" target="_blank">View in browser</a>', '<a class="wr" href="<%@ include view=\'tradepoint_mirror_link_only\' %>" style="font-family:\'Kingfisher\', Arial, Helvetica, sans-serif !important; color:#3c3c3c; text-decoration:underline;" target="_blank">View in browser</a>', $copy);
		}else{
			$copy = '<div style="padding:5px; border:1px solid red; color:#DB0011; margin:5px; font-size:12px; font-weight:bold;"><span style="font-size:16px;">!&nbsp;&nbsp;</span>View Online Link (footer) not found</div>' .  $copy;
		}
	
	

		//LOYALTY BANNER
		$pos6 = stripos($copy, '<div class="pad5" align="center" style=""><div style="width:500px; border: 2px dashed black; padding:20px; margin: 10px;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>');
		$pos6a = stripos($copy, '<div class="pad5" align="center" style="z-index: 110;"><div style="width: 500px; border: 2px dashed black; padding: 20px; margin: 10px; z-index: 111;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>');
		if ($pos6 !== false) {
	    	$copy = str_replace('<div class="pad5" align="center" style=""><div style="width:500px; border: 2px dashed black; padding:20px; margin: 10px;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>', "<%@ include view='Current_Loyalty_Tier_15' %>", $copy);
		}elseif ($pos6a !== false) {
	    	$copy = str_replace('<div class="pad5" align="center" style="z-index: 110;"><div style="width: 500px; border: 2px dashed black; padding: 20px; margin: 10px; z-index: 111;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>', "<%@ include view='Current_Loyalty_Tier_15' %>", $copy);

		}else{
			$copy = '<div style="padding:5px; border:1px solid red; color:#DB0011; margin:5px; font-size:12px; font-weight:bold;"><span style="font-size:16px;">!&nbsp;&nbsp;</span>Loyalty Banner not found</div>' .  $copy;
		}

		//MEMBERSHIP
		$pos7 = stripos($copy, '0123456789');
		if ($pos7 !== false) {
	    	$copy = str_replace('0123456789', "<%= recipient.customerID %>", $copy);
		}else{
			$copy = '<div style="padding:5px; border:1px solid red; color:#DB0011; margin:5px; font-size:12px; font-weight:bold;"><span style="font-size:16px;">!&nbsp;&nbsp;</span>Membership number not found</div>' .  $copy;
		}
		
		//YEAR
		$copy = str_replace('B&amp;Q 2024', 'B&amp;Q <%= formatDate(new Date(), "%4Y") %>', $copy);
		$copy = str_replace('B&amp;Q 2021', 'B&amp;Q <%= formatDate(new Date(), "%4Y") %>', $copy);
		$copy = str_replace('B&amp;Q 2022', 'B&amp;Q <%= formatDate(new Date(), "%4Y") %>', $copy);
		$copy = str_replace('B&amp;Q 2023', 'B&amp;Q <%= formatDate(new Date(), "%4Y") %>', $copy);

		//UNSUB
		$pos8 = stripos($copy, '<a href="#" style="color:#ffffff; text-decoration:underline;" target="_blank">Unsubscribe</a>');
		if ($pos8 !== false) {
	    	$copy = str_replace('<a href="#" style="color:#ffffff; text-decoration:underline;" target="_blank">Unsubscribe</a>', '<a href="<%@ include view=\'tp_unsubscribe_link\' %>" style="color:#ffffff; text-decoration:underline;" target="_blank">Unsubscribe</a>', $copy);
		}else{
			$copy = '<div style="padding:5px; border:1px solid red; color:#DB0011; margin:5px; font-size:12px; font-weight:bold;"><span style="font-size:16px;">!&nbsp;&nbsp;</span>Unsubscribe Link not found</div>' .  $copy;
		}
		
		// FOOTER STUFF (GMAIL APP FIX AND TRACKING)
		$copy = str_replace('</body>', '<div class="gmail-app-fix" style="background-color: #ffffff; color: #ffffff; font-family: monospace; font-size: 15px; line-height: 0;">
	&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div><%@ include view="TradePointTracking" %><%@ include view="MEC_Tags" %></body>', $copy);






	}

	//General Rules

	

?>
