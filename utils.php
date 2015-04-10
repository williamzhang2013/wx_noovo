<?php
//require_once(dirname(__FILE__) . 'global.php');

///////////////////////////////////////////////////////////
//                    For NV Data                        //
///////////////////////////////////////////////////////////
function load_default_data() {
	//global $g_main_state, $g_sub_state;

	// load the default value for all the nvdata
	global $nv_arr;//, $g_main_state, $g_sub_state;
	foreach ($nv_arr as $item){
		$GLOBALS["$item"] = 0;
	}
}

function set_nv_item($line) {
	// parse the '='
	$pos = strpos($line, "=");
	$item = trim(substr($line, 0, $pos));
	$$item = trim(substr($line, $pos+1));
	$GLOBALS["$item"] = $$item;
	echo "line: $line<br> item=$item, value = ${$item}||<br><br>";
}

function load_nv_data() {
	// get all the nv data
	$handler = fopen(NV_FILE, 'r');

	if ($handler == false) {
		// open nv file error!
		load_default_data();
	} else {
		while ($line = fgets($handler)){
			set_nv_item($line);
			//echo "file get line: $line<br>";
		}
		fclose($handler);
	}
}

function save_nv_data() {
	// save the nv data --- whole data
	$handler = fopen(NV_FILE, 'w+');
	global $nv_arr;//, $g_main_state, $g_sub_state;
	foreach ($nv_arr as $item){
		$line = $item . "=" . $GLOBALS["$item"] . "\n" ;
		//echo "save nv data: line=$line<br>";
		//nv_log(__FILE__, __FUNCTION__, "save nv data: line=$line");
		fwrite($handler, $line);
	}
	fclose($handler);
}

///////////////////////////////////////////////////////////
//                    For NV Log                         //
///////////////////////////////////////////////////////////
function nv_clear_log() {
	file_put_contents(LOG_FILE, " ");
}

function nv_begin_log($file, $function, $line) {
	$sfile = trim(substr($file, strripos($file, '/')+1));
	$content = "[". $function. "@". $sfile . "]". $line . "\n";
	file_put_contents(LOG_FILE, $content);
}

// example: nv_log(__FILE__, __FUNCTION__, "hello world!")
function nv_log($file, $function, $line) {
	$sfile = trim(substr($file, strripos($file, '/')+1));
	$content = "[". $function. "@". $sfile . "]". $line . "\n";
	file_put_contents(LOG_FILE, $content, FILE_APPEND);
}
?>