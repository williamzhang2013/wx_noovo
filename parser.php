<?php

// parser user's input
define("INPUT_ELSE",          0);
define("INPUT_ABOUT",         1);
define("INPUT_LUNCH",         2);
define("INPUT_CONTACT",       3);
define("INPUT_TOOLS",         4);
define("INPUT_TOOLS_WEATHER", 41);
define("INPUT_TOOLS_JOKE",    42);

function get_keyword($content)
{
	$keyword = "";

	// judge whether have '+'
	$plusPos = strpos("$content", "+");
	file_put_contents(LOG_FILE, "plusPos = $plusPos\n", FILE_APPEND);

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

function parser_user_input($content)
{
	$arr_keywords = array("about" => INPUT_ABOUT,
	                 "关于"        => INPUT_ABOUT,
	                 "lunch"      => INPUT_LUNCH,
	                 "订餐"        => INPUT_LUNCH,
	                 "吃饭"        => INPUT_LUNCH,
	                 "contact"     => INPUT_CONTACT,
	                 "天气"        => INPUT_TOOLS_WEATHER,
	                 "joke"        => INPUT_TOOLS_JOKE,
	                 "笑话"         => INPUT_TOOLS_JOKE);
	$input_type = INPUT_ELSE;

	file_put_contents(LOG_FILE, "content = $content\n", FILE_APPEND);
	// get the keyword
	$keyword = get_keyword($content);
	file_put_contents(LOG_FILE, "keyword = $keyword\n", FILE_APPEND);
	
	//traverse
	foreach ($arr_keywords as $key => $value) {
		file_put_contents(LOG_FILE, "key=$key\n", FILE_APPEND);
		if (strcmp($keyword, $key) == 0) {
			// find the keyword
			$input_type = $value;			
		}
	}

	return $input_type;
}
?>