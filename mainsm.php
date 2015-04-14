<?php

function handler_help($data) {
	$content = HELLO_ABOUT."\n".HELLO_ORDER."\n".HELLO_CONTACT."\n".HELLO_WEATHER."\n".HELLO_JOKE."\n".HELLO_MUSIC;
	$result = display_text($data[0], $content);
	return $result;
}

function handler_about($data){
	nv_log(__FILE__, __FUNCTION__, "Entry!");
	$arrItem = array();
	$arrItem['Title'] = "Noovo电子简介";
	$arrItem['Description'] = "Noovo Technology Corporation_是一个国际化的科技企业，主要从事开发、设计、生产和销售数字电视相关硬件、软件和服务。特别在Wi-Fi DTV Tuner产品领域，Noovo是主要的供应商之一。";
	$arrItem['PicUrl'] = "http://www.noovo.co/upload_file/page/150/clone_714.jpg";
	$arrItem['Url'] = "http://www.noovo.co/companyinformationtw";
	$articles = array($arrItem);
	$result = display_news($data[0], $articles);	
	return $result;
}

function handler_order($data) {
	global $g_main_state;
	$g_main_state = STATE_ORDER; // init state --> order state
	
	$content = "";
	$obj = $data[0];
	$openID = $obj->FromUserName;
	nv_log(__FILE__, __FUNCTION__, "openID=$openID");
	if(is_user_login($openID)) {
		// already login
		save_usr_main_state($openID);
		
		$content = HELP_MENU_FRY . HELP_MENU_RICE . HELP_ORDER_QUIT; 
	} else {
		// indicate usr login
		$content = PLEASE_LOGIN;
		//user_login($oepnID, $usr);
	}

	//$content = "订餐:Comming soon...";
	$result = display_text($data[0], $content);	
	return $result;
}

function handler_contact($data) {
	$name = $data[1];
	nv_log(__FILE__, __FUNCTION__, "name = $name");
	//$conn = contact_connect();
	$info = contact_query(strtolower(trim($name)));
	
	$content = generate_contact_card($info);
	$result = display_text($data[0], $content);
	return $result;
}

function handler_weather($data) {
	$city = $data[1];
	nv_log(__FILE__, __FUNCTION__, "city = $city");
	
	$url = "http://apix.sinaapp.com/weather/?appkey=".$object->ToUserName."&city=".urlencode($city);
	$output = file_get_contents($url);
	$content = json_decode($output, true);
	$result = display_news($data[0], $content);
	return $result;
}

function handler_joke($data) {
	$url = "http://apix.sinaapp.com/joke/?appkey=trialuser";
	$output = file_get_contents($url);
	$content = json_decode($output, true);
	$result = display_text($data[0],  substr($content, 0, strlen($content)-30));
	return $result;
}

function handler_music($data) {
	$music = $data[1];
	nv_log(__FILE__, __FUNCTION__, "music = $music");
	$music_info = get_music_info($music);
	nv_log(__FILE__, __FUNCTION__, "music_info = $music_info");
	//$result = "";
	if (is_array($music_info)) {
		$result = display_music($data[0], $music_info);
	} else {
		$result = display_text($data[0], $music_info);
	}
	return $result;
}

function handler_minit($event, $data) {
	nv_log(__FILE__, __FUNCTION__, "event=$event");
	$handler_arr = array("handler_help",
			             "handler_help",
			             "handler_about",
			             "handler_order",
			             "handler_contact",
			             "handler_weather",
			             "handler_joke", 
	                     "handler_music");
	$func = $handler_arr[$event];
	$result = $func($data);	
	return $result;
}

//////////////////////////////////////////////////////////////////////////
function handler_order_login_tip($data) {
	$content = PLEASE_LOGIN;
	$result = display_text($data[0], $content);

	return $result;
}

function handler_order_help($data) {
	$content = "This is the order state help!!!";
	$result = display_text($data[0], $content);
	return $result;
}

function handler_order_quit($data) {
	global $g_main_state;
	$g_main_state = STATE_INIT; // init state --> order state
	$obj = $data[0];
	$openID = $obj->FromUserName;
	save_usr_main_state($openID);
	
	$content = AFTER_ORDER_QUIT;
	$result = display_text($data[0], $content);
	return $result;
}

function handler_order_login($data) {
	$content = "login:";
	$result = display_text($data[0], $content);
	return $result;
}

function handler_morder($event, $data) {
	nv_log(__FILE__, __FUNCTION__, "order state");
	$handler_arr = array("handler_order_help",    // 0
			             "handler_order_help",
						 "handler_order_help",
						 "handler_order_help",
						 "handler_order_help",
						 "handler_order_help",   // 5
						 "handler_order_help",
						 "handler_order_help",
				         "handler_order_quit",
						 "handler_order_login");
	$obj = $data[0];
	$openID = $obj->FromUserName;
	$result = "";
	if (is_user_login($openID)) {
		// already login --- normal procedure
		$func = $handler_arr[$event];
		$result = $func($data);
	} else {
		// still not login --- login first
		$result = handler_order_login_tip($data);
	}

	return $result;	
}

?>