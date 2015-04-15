<?php

function handler_idle_help($data) {
	$content = HELLO_ABOUT."\n".HELLO_ORDER."\n".HELLO_CONTACT."\n".HELLO_WEATHER."\n".HELLO_JOKE."\n".HELLO_MUSIC;
	$result = display_text($data[0], $content);
	return $result;
}

function handler_idle_about($data){
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

function handler_idle_order($data) {
	global $g_main_state;
	
	$content = "";
	$obj = $data[0];
	$openID = $obj->FromUserName;
	nv_log(__FILE__, __FUNCTION__, "openID=$openID");
	if(is_user_login($openID)) {
		// already login
		$g_main_state = STATE_ORDER; // init state --> order state
		save_usr_main_state($openID);
		
		$content = AFTER_ORDER_QUIT . HELP_MENU_FRY . HELP_MENU_RICE;
		$content .= HELP_ORDER_ORDER . "\n" . HELP_ORDER_DEL . "\n" . HELP_ORDER_DELALL . "\n" . HELP_ORDER_LIST . "\n";
		$content .= HELP_ORDER_QUIT;
	} else {
		// indicate usr login
		$g_main_state = STATE_LOGIN; // init state --> login state
		$content = PLEASE_LOGIN;
	}

	$result = display_text($data[0], $content);	
	return $result;
}

function handler_idle_contact($data) {
	$name = $data[1];
	nv_log(__FILE__, __FUNCTION__, "name = $name");
	//$conn = contact_connect();
	$info = contact_query(strtolower(trim($name)));
	
	$content = generate_contact_card($info);
	$result = display_text($data[0], $content);
	return $result;
}

function handler_idle_weather($data) {
	$city = $data[1];
	nv_log(__FILE__, __FUNCTION__, "city = $city");
	
	$url = "http://apix.sinaapp.com/weather/?appkey=".$object->ToUserName."&city=".urlencode($city);
	$output = file_get_contents($url);
	$content = json_decode($output, true);
	$result = display_news($data[0], $content);
	return $result;
}

function handler_idle_joke($data) {
	$url = "http://apix.sinaapp.com/joke/?appkey=trialuser";
	$output = file_get_contents($url);
	$content = json_decode($output, true);
	$result = display_text($data[0],  substr($content, 0, strlen($content)-30));
	return $result;
}

function handler_idle_music($data) {
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
	$result = "";
	switch ($event) {
		case EVENT_ABOUT:
			$result = handler_idle_about($data);
			break;
		case EVENT_ORDER:
			$result = handler_idle_order($data);
			break;
		case EVENT_CONTACT:
			$result = handler_idle_contact($data);
			break;
		case EVENT_WEATHER:
			$result = handler_idle_weather($data);
			break;
		case EVENT_JOKE:
			$result = handler_idle_joke($data);
			break;
		case EVENT_MUSIC:
			$result = handler_idle_music($data);
			break;
		default:
			$result = handler_idle_help($data);
			break;
	}
// 	$handler_arr = array("handler_help",
// 			             "handler_help",
// 			             "handler_about",
// 			             "handler_order",
// 			             "handler_contact",
// 			             "handler_weather",
// 			             "handler_joke", 
// 	                     "handler_music");
// 	$func = $handler_arr[$event];
// 	$result = $func($data);	
	return $result;
}

//////////////////////////////////////////////////////////////////////////
function handler_login_login($data) {
	$obj = $data[0];
	$usrname = $data[1];
	$openID = $obj->FromUserName;
	$content = "";
	$result = "";
	
	$nvadmin = ADMIN_NAME . '@' .ADMIN_PWD;
	if (strcmp($usrname, $nvadmin) == 0) {
		// admin login
		$GLOBALS['g_main_state'] = STATE_ADMIN;
		save_usr_main_state($openID);
		$content = HELLO_ADMIN;
	} else {
		// check if user if noovo employee
		if (is_user_nv_employee($usrname)) {
			// is nv employee, login!
			$GLOBALS['g_main_state'] = STATE_ORDER;
			save_usr_main_state($openID);
			
			// save to db
			user_login($openID, $usrname);
			$content = HELP_MENU_FRY . HELP_MENU_RICE;
			$content .= HELP_ORDER_ORDER . "\n" . HELP_ORDER_DEL . "\n" . HELP_ORDER_DELALL . "\n" . HELP_ORDER_LIST . "\n";
			$content .= HELP_ORDER_QUIT; 
		} else {
			// not nv employee
			$content = HELP_NOT_NVNESE . "\n" . PLEASE_LOGIN;
		}
	}	
	$result = display_text($obj, $content);
	return result;
}

function handler_mlogin($event, $data) {
	$result = "";
	$obj = $data[0];
	$openID = $obj->FromUserName;
	switch($event) {
		case EVENT_QUIT: 
			// quit to init state
			$GLOBALS['g_main_state'] = STATE_IDLE;
			save_usr_main_state($openID);
			$result = handler_idle_help($data);
			break;
		case EVENT_LOGIN:
		default:
			// login admin --- admin state; login usr --- order state
			$result = handler_login_login($data);
			break;
	}
	
	return $result;
}

//////////////////////////////////////////////////////////////////////////
function handler_admin_help($data) {
	$content = HELP_ADMIN_LOCK . "\n" .HELP_ADMIN_UNLOCK . "\n";
	$content .= HELP_ADMIN_QUERY_TODAY . "\n" . HELP_ADMIN_QUERY_DATE . "\n" . HELP_ADMIN_QUERY_WEEK . "\n" . HELP_ADMIN_QUERY_MONTH;
	$result = display_text($data[0], $content);
	
	return $result;
}

function handler_admin_query($data) {
	$type = strtolower(trim($data[1]));
	$query_date = date(Y-m-d);
	$content = "";
	$result = "";
	
	nv_log(__FILE__, __FUNCTION__, "query type=$content");	
	if (strcmp($type, "today") == 0) {
		// collect all today's order
		$order = get_today_order();
		if (count($order) == 0) {
			$content = TODAY_NO_ORDER;
		} else {
			foreach ($order as $item) {
				$content .= $item[0] . "    x" . $item[3]/$item[2]."---".$item[3]."\n";
			}
// 			while (list($course, $price, $sum) = each($order)) {
// 				$content .= $course . "    x" . $sum/$price."---".$sum."\n"; 
// 			}
		}
	} else if (strcmp($type, "week") == 0) {
		// collect this week's order
		$content = "Comming soon...";
	} else if (strcmp($type, "month") == 0) {
		// collect this month's order
		$content = "Comming soon...";
	} else if(strcmp($type, "month") == 0) {
		// collect the date's order
		$content = "Comming soon...";
	} else {
		// wrong format
		$content = HELP_ADMIN_LOCK . "\n" .HELP_ADMIN_UNLOCK . "\n";
		$content .= HELP_ADMIN_QUERY_TODAY . "\n" . HELP_ADMIN_QUERY_DATE . "\n" . HELP_ADMIN_QUERY_WEEK . "\n" . HELP_ADMIN_QUERY_MONTH;
	}
	$result = display_text($data[0], $content);
	return $result;
}

function handler_madmin($event, $data) {
	$result = "";
	$obj = $data[0];
	$openID = $obj->FromUserName;
	
	switch ($event) {
		case EVENT_QUIT:
			// quit to init state
			$GLOBALS['g_main_state'] = STATE_IDLE;
			save_usr_main_state($openID);
			$result = handler_idle_help($data);
			break;
		case EVENT_LOCK:
			// lock the order system --- user can't order course now
			lock_order_system();
			break;
		case EVENT_UNLOCK:
			// unlock the order system
			unlock_order_system();
			break;
		case EVENT_DEL:
			// delete a course in one user
			break;
		case EVENT_QUERY:
			$result = handler_admin_query($data);
			break;
		case EVENT_HELP:
		default:
			$result = handler_admin_help($data);
			break;
	}
	return $result;
}

//////////////////////////////////////////////////////////////////////////
function handler_order_login_tip($data) {
	$content = PLEASE_LOGIN;
	$result = display_text($data[0], $content);

	return $result;
}

function handler_order_help($data) {
	$content = HELP_MENU_FRY . HELP_MENU_RICE;
	$content .= HELP_ORDER_ORDER . "\n" . HELP_ORDER_DEL . "\n" . HELP_ORDER_DELALL . "\n" . HELP_ORDER_LIST . "\n";
	$content .= HELP_ORDER_QUIT;
	$result = display_text($data[0], $content);
	return $result;
}

function handler_order_quit($data) {
	global $g_main_state;
	$g_main_state = STATE_IDLE; // init state --> order state
	$obj = $data[0];
	$openID = $obj->FromUserName;
	save_usr_main_state($openID);
	
	$content = AFTER_ORDER_QUIT;
	$result = display_text($data[0], $content);
	return $result;
}

function handler_order_login($data) {
	// only admin can login
	$obj = $data[0];
	$openID = $obj->FromUserName;
	$usrname = $data[1];
	$nvadmin = ADMIN_NAME . '@' .ADMIN_PWD;
	$content = "";
	if (strcmp($usrname, $nvadmin) == 0) {
		// admin login
		$GLOBALS['g_main_state'] = STATE_ADMIN;
		save_usr_main_state($openID);
		$content = HELLO_ADMIN;
	}	
	$result = display_text($data[0], $content);
	return $result;
}

function handler_order_list_menu($data) {
	$menu_type = $data[1];
	
	$lsmenu_arr = array(NAME_FRYING => FRYING, NAME_RICE    => RICE, 
			            NAME_SOUP   => SOUP,   NAME_SNACK   => SNACK,
	                    NAME_ATEA   => ATEA,   NAME_MARMITE => MARMITE);
	$type = $lsmenu_arr[$menu_type];
	nv_log(__FILE__, __FUNCTION__, "menu type=$type");
	
	$content = "";
	$menu = list_menu($type);	
	foreach ($menu as $item) {
		$content .= $item[0] . "  " . $item[1] . "\n";
	}
// 	while (list($course, $price) = each($menu)) {
// 		$content .= $course . "    " . $price . "\n";
// 	}
	
	$result = display_text($data[0], $content);	
	return $result;
}

function handler_order_rsv($data) {
	//
}

function handler_order_del($data) {
	//
}

function handler_morder($event, $data) {
	nv_log(__FILE__, __FUNCTION__, "order state");
	$result = "";
	
	switch ($event) {
		case EVENT_LOGIN:
			$result = handler_order_login($data);
			break;
		case EVENT_QUIT:
			$result = handler_order_quit($data);
			break;
		case EVENT_LSMENU:
			$result = handler_order_list_menu($data);
			break;
		case EVENT_RSV:
			$result = handler_order_rsv($data);
			break;
		case EVENT_DEL:
			$result = handler_order_del($data);
			break;
		default:
			$result = handler_order_help($data);
			break;
	}
	
// 	$handler_arr = array("handler_order_help",    // 0
// 			             "handler_order_help",
// 						 "handler_order_help",
// 						 "handler_order_help",
// 						 "handler_order_help",
// 						 "handler_order_help",   // 5
// 						 "handler_order_help",
// 						 "handler_order_help",
// 				         "handler_order_quit",
// 						 "handler_order_login");
// 	$obj = $data[0];
// 	$openID = $obj->FromUserName;
// 	$result = "";
	
// 	// already login --- normal procedure
// 	$func = $handler_arr[$event];
// 	$result = $func($data);

	return $result;	
}

?>