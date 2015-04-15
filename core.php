<?php

nv_begin_log(__FILE__, __FUNCTION__, "Main Entry");
if (isset($_GET['echostr'])) {
    valid();
} else {
    responseMsg();
}

function checkSignature()
{
	// you must define TOKEN by yourself
	if (!defined("TOKEN")) {
		throw new Exception('TOKEN is not defined!');
	}

	$signature = $_GET["signature"];
	$timestamp = $_GET["timestamp"];
	$nonce = $_GET["nonce"];

	$token = TOKEN;
	$tmpArr = array($token, $timestamp, $nonce);
	// use SORT_STRING rule
	sort($tmpArr, SORT_STRING);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );

	if( $tmpStr == $signature ){
		return true;
	}else{
		return false;
	}
}

function valid()
{
	$echoStr = $_GET["echostr"];

	//valid signature , option
	if(checkSignature()){
		echo $echoStr;
		exit;
	}
}

function receiveEvent($object)
{
	$content = "";
	switch ($object->Event) {
		case "subscribe":
			$content = "欢迎订阅Noovo电子"."\n\n".HELLO_MSG;
			break;
		case "unsubscribe":
			$content = "Bye-Bye";
			break;
		default:
			$content = "event = $object->Event";
			break;
	}
	$result = display_text($object, $content); //transmitText($object, $content);
	return $result;
}

function receiveText($obj)
{
	$text = trim($obj->Content);
	nv_log(__FILE__, __FUNCTION__,  "receive text: $obj->Content, text=$text");
	
	// prehandle the input --- get the event & data
	$event = get_event($text);
	$data[0] = $obj;
	$data[1] = get_input_real_content($text);
	
	// run the handler
	global $g_main_state;
	$handler_arr = array("handler_midle", "handler_morder", "handler_mlogin", "handler_madmin");
	$handler = $handler_arr[$g_main_state];
	nv_log(__FILE__, __FUNCTION__, "handler = $handler");
	$result = $handler($event, $data);
	nv_log(__FILE__, __FUNCTION__, "result = $result");
	return $result;
}

function responseMsg()
{
	//get post data, May be due to the different environments
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

	//extract post data
	if (!empty($postStr)){
		/* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
		 the best way is to check the validity of xml by yourself */
		libxml_disable_entity_loader(true);
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		
		$GLOBALS['g_main_state'] = load_usr_main_state($postObj->FromUserName);
		$result = "";
		$rx_type = trim($postObj->MsgType);
		switch ($rx_type) {
			case "event":
				$result = receiveEvent($postObj);
				break;
			case "text":
				$result = receiveText($postObj);
				break;
			default:
				$result = "Import something...";
				break;
		}
		echo $result;
	}else {
		echo "";
		exit;
	}
}

?>