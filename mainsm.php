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
		// save the openID -- user login
		if (is_new_user($openID)) {
			add_new_user($openID);
		}
		
		// indicate usr login
		$g_main_state = STATE_LOGIN; // init state --> login state
		save_usr_main_state($openID);
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

function handler_midle($event, $data) {
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
	
	return $result;
}

//////////////////////////////////////////////////////////////////////////
function handler_login_login($data) {
	$obj = $data[0];
	$usrname = $data[1];
	$openID = $obj->FromUserName;
	$content = "";
	$result = "";
	
	nv_log(__FILE__, __FUNCTION__, "usrname=$usrname");
	//$nvadmin = ADMIN_NAME . '@' .ADMIN_PWD;
	$nvadmin = split("@|＠", $usrname);
	nv_log(__FILE__, __FUNCTION__, "name = $nvadmin[0], password=$nvadmin[1]");
	if (strcmp($nvadmin[0], ADMIN_NAME) == 0 && strcmp($nvadmin[1], ADMIN_PWD) == 0) {
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
	return $result;
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
	$content = HELP_ADMIN_LOCK . "\n" .HELP_ADMIN_UNLOCK . "\n" . HELP_ADMIN_DEL . "\n";
	$content .= HELP_ADMIN_QUERY_TODAY . "\n" . HELP_ADMIN_QUERY_DATE . "\n" . HELP_ADMIN_QUERY_MONTH. "\n" . HELP_ADMIN_QUERY_USR;
	$result = display_text($data[0], $content);
	
	return $result;
}

function handler_admin_list_today($data) {
	$content = "";
	// collect all today's order
	$order = get_today_order();
	if (count($order) == 0) {
		$content = TODAY_NO_ORDER;
	} else {
		$sum = 0;
		$dishs = 0;
		foreach ($order as $item) {
			$sum += $item[2];
			$dishs += $item[2]/$item[1];
			$typename = get_course_type_name($item[3]);
			nv_log(__FILE__, __FUNCTION__, "order item=$item[0], $item[1], $item[2]");
			$content .= $item[0] . "(".$typename. ")        x" . $item[2]/$item[1]."    ----".$item[2]."\n";
		}
		$content .= "总计:  " .$dishs . "份   金额:" .$sum;
	}
	
	$result = display_text($data[0], $content);
	return $result;
}

function handler_admin_list_date($data) {
	$content = "";
	$lsstr = strtolower(trim($data[1]));
	$conditions = split("@|＠", $lsstr);
	$lsdate = $conditions[1];
	$ymd = split("-", $lsdate);
	//$year = date("Y");
	//if ($ymd[0] <= $year && $ymd[1] <=12 && $ymd[2] <=31)
	if (strlen($ymd[0]) == 0 || strlen($ymd[1]) == 0 || strlen($ymd[2]) == 0) {
		$content = HELP_ADMIN_QUERY_DATE . "\n";
	} else {
		$day = date("Y-m-d", mktime(0,0,0,$ymd[1],$ymd[2],$ymd[0]));
		//
		$order = get_date_order($day);
		if (count($order) == 0) {
			$content = TODAY_NO_ORDER;
		} else {
			$sum = 0;
			$dishs = 0;
			foreach ($order as $item) {
				$sum += $item[2];
				$dishs += $item[2]/$item[1];
				$typename = get_course_type_name($item[3]);
				nv_log(__FILE__, __FUNCTION__, "order item=$item[0], $item[1], $item[2]");
				$content .= $item[0] . "(".$typename. ")        x" . $item[2]/$item[1]."    ----".$item[2]."\n";
			}
			$content .= "总计:  " .$dishs . "份   金额:" .$sum;
		}
	}
	
	$result = display_text($data[0], $content);
	return $result;	
}

function handler_admin_list_month($data) {
	$content = "";
	$lsstr = strtolower(trim($data[1]));
	$conditions = split("@|＠", $lsstr);
	$lsdate = $conditions[1];

	
	$sum = get_month_sum($lsdate);
	$content .= $lsdate ."月总金额:  " .$sum;
	$result = display_text($data[0], $content);
	return $result;
}

function handler_admin_list_usr($data) {
	$days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	$lsstr = strtolower(trim($data[1]));
	$conditions = split("@|＠", $lsstr);
	
	// get openID from usr
	$usr = $conditions[1];
	$openID = get_usr_openid($usr);
	nv_log(__FILE__, __FUNCTION__, "usr=$usr, openID=$openID");
	
	// get the year & month
	$lsdate = $conditions[2];
	$ymd = split("-", $lsdate);
	$year = (int)$ymd[0];
	$month = (int)$ymd[1];
	nv_log(__FILE__, __FUNCTION__, "year=$year, month=$month");
	
	$content = $usr. ": " . $lsdate . "\n";
	$sum = 0;
	$days[1] += is_leap_year($year);
	for ($i=1; $i<=$days[$month]; $i++) {
		$day = date("Y-m-d", mktime(0,0,0,$month,$i,$year));
		$day_sum = get_date_usr_sum($openID, $day);
		if ($day_sum > 0) {
			$sum += $day_sum;
			$content .= $day . "        --- ".$day_sum . "\n";
		}
	}
	$content .= "金额: " . $sum;
	$result = display_text($data[0], $content);
	
	return $result;
}

function handler_admin_list($data) {
	$lsstr = strtolower(trim($data[1]));
	$now = date("Y-m-d");
	$content = "";
	$result = "";
	
	$type = get_admin_ls_type($lsstr);
	switch ($type) {
		case ADMIN_LS_TODAY:
			$result = handler_admin_list_today($data);
			break;
		case ADMIN_LS_DATE:
			$result = handler_admin_list_date($data);
			break;
		case ADMIN_LS_MONTH:
			$result = handler_admin_list_month($data);
			break;
		case ADMIN_LS_USR:
			$result = handler_admin_list_usr($data);
			break;
		default:
			$content = HELP_ADMIN_LOCK . "\n" .HELP_ADMIN_UNLOCK . "\n" . HELP_ADMIN_DEL . "\n";
			$content .= HELP_ADMIN_QUERY_TODAY . "\n" . HELP_ADMIN_QUERY_DATE . "\n" . HELP_ADMIN_QUERY_MONTH . "\n" . HELP_ADMIN_QUERY_USR;
			$result = display_text($data[0], $content);
			break;
	}
	return $result;	
}

function handler_admin_query($data) {
	$type = strtolower(trim($data[1]));
	$query_date = date("Y-m-d");
	$content = "";
	$result = "";
	
	nv_log(__FILE__, __FUNCTION__, "query type=$content");	
	if (strcmp($type, "today") == 0) {
		// collect all today's order
		$order = get_today_order();
		if (count($order) == 0) {
			$content = TODAY_NO_ORDER;
		} else {
			$sum = 0;
			foreach ($order as $item) {
				$sum += $item[2];
				$typename = get_course_type_name($item[3]);
				nv_log(__FILE__, __FUNCTION__, "order item=$item[0], $item[1], $item[2]");
				$content .= $item[0] . "(".$typename. ")        x" . $item[2]/$item[1]."    ----".$item[2]."\n";
			}
			$content .= "总计:  " .$sum;
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
		$content = HELP_ADMIN_LOCK . "\n" .HELP_ADMIN_UNLOCK . "\n" . HELP_ADMIN_DEL . "\n";
		$content .= HELP_ADMIN_QUERY_TODAY . "\n" . HELP_ADMIN_QUERY_DATE . "\n" . HELP_ADMIN_QUERY_MONTH . "\n" . HELP_ADMIN_QUERY_USR;
	}
	$result = display_text($data[0], $content);
	return $result;
}

function handler_admin_lock($data) {
	// lock the order system --- user can't order course now
	lock_order_system();
	$content = ORDER_SYS_LOCKED;
	$result = display_text($data[0], $content);
	return $result;
}

function handler_admin_unlock($data) {
	// unlock the order system
	unlock_order_system();
	$content = ORDER_SYS_UNLOCKED;
	$result = display_text($data[0], $content);
	return $result;
}

function handler_admin_del($data) {
	$usr_courses = split("@|＠", $data[1]);
	$usr = $usr_courses[0];
	$content = "";
	
	if (is_user_nv_employee($usr)) {
		$openID = get_usr_openid($usr);
		array_shift($usr_courses);
		delete_courses($openID, $usr_courses);
		$content = "您取消了" . $usr ."的:";
		foreach ($usr_courses as $course) {
			$content .= "\n" . $course;
		}		
	} else {
		// wrong usr
		$content = NO_USR_FOUND . $usr;
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
			$result = handler_admin_lock($data);
			break;
		case EVENT_UNLOCK:
			$result = handler_admin_unlock($data);
			break;
		case EVENT_DEL:
			$result = handler_admin_del($data);
			break;
		case EVENT_LIST:
			$result = handler_admin_list($data);
			break;
// 		case EVENT_QUERY:
// 			$result = handler_admin_query($data);
// 			break;
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
	
	$content = HELLO_ABOUT."\n".HELLO_ORDER."\n".HELLO_CONTACT."\n".HELLO_WEATHER."\n".HELLO_JOKE."\n".HELLO_MUSIC;
	$result = display_text($data[0], $content);
	return $result;
}

function handler_order_login($data) {
	// only admin can login
	$obj = $data[0];
	$openID = $obj->FromUserName;
	$usrname = $data[1];
	$content = "";
	
	//$nvadmin = ADMIN_NAME . '@' .ADMIN_PWD;
	$nvadmin = split("@|＠", $usrname);
	nv_log(__FILE__, __FUNCTION__, "name = $nvadmin[0], password=$nvadmin[1]");
	if (strcmp($nvadmin[0], ADMIN_NAME) == 0 && strcmp($nvadmin[1], ADMIN_PWD) == 0) {
		// admin login
		$GLOBALS['g_main_state'] = STATE_ADMIN;
		save_usr_main_state($openID);
		$content = HELLO_ADMIN;
		$result = display_text($data[0], $content);
	} else {
		$result = handler_order_help($data);
	}	
	
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
	$total = 0;	
	foreach ($menu as $item) {
		if (++$total <= LINES_PER_PAGE) {
			$content .= $item[0] . "  " . $item[1] . "\n";
		}
	}
	
	$result = display_text($data[0], $content);	
	return $result;
}

function handler_order_next($data) {
	$result = "";
	return $result;
}

function handler_order_list($data) {
	$result = "";
	$content = "";
	$obj = $data[0];
	$openID = $obj->FromUserName;
	$courses = get_today_usr_order($openID);
	
	if (count($courses) == 0) {
		$content = TODAY_USR_NOORDER;
	} else {
		$content = TODAY_USR_ORDER;
		foreach ($courses as $course) {
			$type  = get_course_type($course);
			$typename = get_course_type_name($type);
			$content .= "\n" . $course . "    " . $typename;
		}
	}
	
	$result = display_text($obj, $content);
	return $result;
}

function handler_order_rsv($data) {
	$content = "";
	$result = "";
	if (is_order_locked() == 1) {
		// order system locked!
		$content = ORDER_SYS_LOCKED;
		$result = display_text($data[0], $content);
	} else {
		$result = handler_order_rsv_unlocked($data);
	}
	
	return $result;
}

function handler_order_rsv_unlocked($data) {
	$result = "";
	$obj = $data[0];
	$openID = $obj->FromUserName;
	$courses = get_course_list($data[1]);
	
	$content = "您订了:";
	//order_courses($openID, $courses);
	foreach ($courses as $course) {
		$type = get_course_type($course);
		$typename = get_course_type_name($type);
		nv_log(__FILE__, __FUNCTION__, "type=$type");
		if ( FRYING <= $type && $type <= MARMITE) {
			order_one_course($openID, $course, $type);
			$content .= "\n" . $course . "   ". $typename;
		}		
	}
	$result = display_text($data[0], $content);
	return $result;
}

function handler_order_del($data) {
	$content = "";
	$result = "";
	if (is_order_locked() == 1) {
		// order system locked!
		$content = ORDER_SYS_LOCKED;
		$result = display_text($data[0], $content);
	} else {
		$result = handler_order_del_unlocked($data);
	}
	
	return $result;
}

function handler_order_del_unlocked($data) {
	$result = "";
	$obj = $data[0];
	$openID = $obj->FromUserName;
	$type = strtolower(trim($data[1]));
	
	$courses = array();
	if (strcmp($type, "all") == 0) {
		$courses = get_today_usr_order($openID);
	} else {
		$courses = get_course_list($data[1]);
	}
	
	$content = "您取消了:";
	delete_courses($openID, $courses);
	foreach ($courses as $course) {
		nv_log(__FILE__, __FUNCTION__, "del course: $course");
		$content .= "\n" . $course;
	}
	$result = display_text($data[0], $content);
	return $result;
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
		case EVENT_NEXT:
			$result = handler_order_next($data);
			break;
		case EVENT_LIST:
			$result = handler_order_list($data);
			break;
		default:
			$result = handler_order_help($data);
			break;
	}
	
	return $result;	
}

?>