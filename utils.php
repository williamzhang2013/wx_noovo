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
//                    For Database                       //
///////////////////////////////////////////////////////////
function db_connect() {
	$conn = mysql_connect("localhost", "root", "root");
	if(!$conn) {
		$err = mysql_error();
		nv_log(__FILE__, __FUNCTION__, "Could not connect: $err");
		exit(0);
	}
		
	return $conn;
}

function contact_query_with_ename($ename) {
	//
}

function contact_query($name) {
	nv_log(__FILE__, __FUNCTION__, "contact_query!");
	$conn = db_connect(); //mysql_connect("localhost", "root", "root");
	if(!$conn) {
		//die('Could not connect: ' . mysql_error());
		exit(0);
	}
	mysql_select_db("nv_contact", $conn);

	$result = mysql_query("SELECT * FROM EmployeeInfo");
	while($row = mysql_fetch_array($result)) {
		$eName = $row['eName'];
		if (contact_ename_match($name, $eName)) {
			// FOUND!!!
			nv_log(__FILE__, __FUNCTION__, "Find the match record!");
			return $row;
		}
	}
	
	mysql_free_result($result);
	return false;
}

function menu_search() {
	//
}

function order_login() {
	//
}

function is_user_login($openID) {
	$eName = "";
	$conn = db_connect();
	if(!$conn) {
		//die('Could not connect: ' . mysql_error());
		exit(0);
	}
	mysql_select_db("nv_contact", $conn);
	
	$sql = "SELECT * FROM UserInfo WHERE openID = '" .$openID . "'";
	$result = mysql_query($sql);
	if (!$result) {
		$err = mysql_error();
		nv_log(__FILE__, __FUNCTION__, "Could not query:$err");
		exit(0);
	} else {
		if ($row = mysql_fetch_array($result)) {
			$eName = $row['eName'];
		}
	}
	
	mysql_free_result($result);
	nv_log(__FILE__, __FUNCTION__, "eName = $eName");
	if (strlen($eName)) {
		return true;
	} else {
		return false;
	}
}

function user_login($openID, $usr) {
	$conn = db_connect();
	if(!$conn) {
		//die('Could not connect: ' . mysql_error());
		exit(0);
	}
	mysql_select_db("nv_contact", $conn);
	
	$result = mysql_query("SELECT * FROM UserInfo WHERE openID = $openID");
	if (!$result) {
		$err = mysql_error();
		nv_log(__FILE__, __FUNCTION__, "Could not query:$err");
		exit(0);
	} else {
		$sql = "";
		if ($row = mysql_fetch_array($result)) {
			$sql = "UPDATE UserInfo SET eName = '" .$usr ."' WHERE openID = '" . $openID . "'";
			nv_log(__FILE__, __FUNCTION__, "find the openID, but no eName, use update sql: $sql");
		} else {
			$sql = "INSERT INTO UserInfo (version, openID, eName, mState, rsv0, rsv1) VALUES ('";
			$sql.= USER_INFO_VER . "', '" . $openID ."', '" . "$usr" ."', '" .$GLOBALS['$g_main_state'] . "', '', '')"; 
			nv_log(__FILE__, __FUNCTION__, "NOT find the openID, use insert sql: $sql ");
			//mysql_query("INSERT INTO UserInfo (version, openID, eName, mState, rsv0, rsv1) VALUES ('1.0.0', 'gh_82eb59bbc333', 'william.zhang', '', '', '')");
		}
		mysql_query($sql);
	}
}

function load_usr_main_state($openID) {
	$conn = db_connect();
	if(!$conn) {
		//die('Could not connect: ' . mysql_error());
		exit(0);
	}
	mysql_select_db("nv_contact", $conn);
	
	$sql = "SELECT * FROM UserInfo WHERE openID = '" .$openID . "'";
	$result = mysql_query($sql);
	$state = STATE_INIT;
	$row = mysql_fetch_array($result);
	if ($row) {
		$state =  $row['mState'];
	}
	
	nv_log(__FILE__, __FUNCTION__, "state = $state");
	return $state;
}

function save_usr_main_state($openID) {
	global $g_main_state;
	$conn = db_connect();
	if(!$conn) {
		//die('Could not connect: ' . mysql_error());
		exit(0);
	}
	mysql_select_db("nv_contact", $conn);
	
	$sql = "SELECT * FROM UserInfo WHERE openID = '" .$openID . "'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$sql = "UPDATE UserInfo SET mState = '" .$g_main_state ."' WHERE openID = '" . $openID . "'";
	mysql_query($sql);
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