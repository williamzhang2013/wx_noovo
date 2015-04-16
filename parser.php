<?php

// parser user's input
function get_keyword($content)
{
	$keyword = "";

	// judge whether have '+'
	$plusPos = strpos("$content", "+");
	//file_put_contents(LOG_FILE, "plusPos = $plusPos\n", FILE_APPEND);

	if ($plusPos == false) {
		// don't find the +, then keyword is the whole content
		$keyword = $content;
	} else {
		// find the +, keyword is the string before +
		$keyword = substr($content, 0, $plusPos);
	}

	return $keyword;
}

function get_input_real_content($content)
{
	$real_content = "";

	//file_put_contents(LOG_FILE, "content = $content\n", FILE_APPEND);

	// judge whether have '+'
	$plusPos = strpos("$content", "+");

	if ($plusPos == false) {
		// don't find the +, then keyword is the whole content
		//$keyword = $content;
	} else {
		// find the +, keyword is the string before +
		$real_content = substr($content, $plusPos+1);
	}

	return $real_content;
}

function get_event($content)
{
	$arr_keywords = array("about" => EVENT_ABOUT,
			"关于"        => EVENT_ABOUT,
			"order"      => EVENT_ORDER,
			"订餐"        => EVENT_ORDER,
			"吃饭"        => EVENT_ORDER,
			"contact"     => EVENT_CONTACT,
			"天气"        => EVENT_WEATHER,
			"joke"        => EVENT_JOKE,
			"笑话"         => EVENT_JOKE,
			"music"       => EVENT_MUSIC,
			"音乐"         => EVENT_MUSIC,
	        "exit"        => EVENT_QUIT,
	        "quit"        => EVENT_QUIT,
	        "login"       => EVENT_LOGIN,
			"lock"        => EVENT_LOCK,
			"unlock"      => EVENT_UNLOCK,
			"del"         => EVENT_DEL,
	        "sum"         => EVENT_QUERY,
	        "menu"        => EVENT_LSMENU,
	        "rsv"         => EVENT_RSV,
	        "next"        => EVENT_NEXT,
	        "ls"          => EVENT_LIST, 
	        "list"        => EVENT_LIST);
	$event = EVENT_UNDEF;
	$keyword = get_keyword($content);
	foreach ($arr_keywords as $key => $value) {
		nv_log(__FILE__, __FUNCTION__, "key=$key");
		if (strcmp($keyword, $key) == 0) {
			// find the keyword
			$event = $value;
			break;
		}
	}
	return $event;	
}

// input:  $order[string]     --- a list of course
// output: $course_arr[array] --- array
function get_course_list($order) {
	$courses = split("@|＠", $order);
	foreach ($courses as $course) {
		nv_log(__FILE__, __FUNCTION__, "course = $course");
	}
	return $courses;
}

// function parser_user_input($content)
// {
// 	$arr_keywords = array("about" => INPUT_ABOUT,
// 	                 "关于"        => INPUT_ABOUT,
// 	                 "lunch"      => INPUT_LUNCH,
// 	                 "订餐"        => INPUT_LUNCH,
// 	                 "吃饭"        => INPUT_LUNCH,
// 	                 "contact"     => INPUT_CONTACT,
// 	                 "天气"        => INPUT_TOOLS_WEATHER,
// 	                 "joke"        => INPUT_TOOLS_JOKE,
// 	                 "笑话"         => INPUT_TOOLS_JOKE,
// 	                 "music"       => INPUT_TOOLS_MUSIC,
// 	                 "音乐"         => INPUT_TOOLS_MUSIC,);
// 	$input_type = INPUT_ELSE;

// 	nv_log(__FILE__, __FUNCTION__, "content = $content");
// 	// get the keyword
// 	$keyword = get_keyword($content);
// 	nv_log(__FILE__, __FUNCTION__, "keyword = $keyword");
	
// 	//traverse
// 	foreach ($arr_keywords as $key => $value) {
// 		nv_log(__FILE__, __FUNCTION__, "key=$key");
// 		if (strcmp($keyword, $key) == 0) {
// 			// find the keyword
// 			$input_type = $value;	
// 			break;		
// 		}
// 	}

// 	return $input_type;
// }
?>