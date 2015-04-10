<?php

nv_begin_log(__FILE__, __FUNCTION__, "Main Entry");
load_nv_data();
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

function transmitNews($object, $newsArray)
{
	if(!is_array($newsArray)){
		return "";
	}
	$itemTpl = "    <item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>";

	$item_str = "";
	foreach ($newsArray as $item){
		$item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
	}
	$newsTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[news]]></MsgType>
	<Content><![CDATA[]]></Content>
	<ArticleCount>%s</ArticleCount>
	<Articles>
	$item_str</Articles>
	</xml>";

	$result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
	return $result;
}

function receiveText($obj)
{
	$text = trim($obj->Content);
	nv_log(__FILE__, __FUNCTION__,  "receive text: $obj->Content, text=$text");
	
	// run the handler
	global $handler_arr, $g_main_state;
	$handler = $handler_arr[$g_main_state];
	nv_log(__FILE__, __FUNCTION__, "handler = $handler");
	$handler();
	

	//get the input type
// 	$input_type = parser_user_input($text);
// 	$result = "";
// 	nv_log(__FILE__, __FUNCTION__, "input_type = $input_type");
// 	switch ($input_type) {
// 		case INPUT_ABOUT:
// 			$arrItem = array();
// 			$arrItem['Title'] = "Noovo电子简介";
// 			$arrItem['Description'] = "Noovo Technology Corporation_是一个国际化的科技企业，主要从事开发、设计、生产和销售数字电视相关硬件、软件和服务。特别在Wi-Fi DTV Tuner产品领域，Noovo是主要的供应商之一。";
// 			$arrItem['PicUrl'] = "http://www.noovo.co/upload_file/page/150/clone_714.jpg";
// 			$arrItem['Url'] = "http://www.noovo.co/companyinformationtw";
// 			$articles = array($arrItem);
// 			$result = transmitNews($obj, $articles);
// 			break;

// 		case INPUT_CONTACT:
// 			$name = get_input_real_content($text);
// 			nv_log(__FILE__, __FUNCTION__, "name = $name");
// 			//$conn = contact_connect();
// 			$info = contact_query(strtolower(trim($name)));

// 			$content = generate_contact_card($info);
// 			$result = display_text($obj, $content);
// 			break;

// 		case INPUT_LUNCH:
// 			$content = "订餐:Comming soon...";
// 			$result = display_text($obj, $content);
// 			break;

// 		case INPUT_TOOLS_WEATHER:
// 			$city = get_input_real_content($text);
// 			nv_log(__FILE__, __FUNCTION__, "city = $city");

// 			$url = "http://apix.sinaapp.com/weather/?appkey=".$object->ToUserName."&city=".urlencode($city);
// 			$output = file_get_contents($url);
// 			$content = json_decode($output, true);
// 			$result = transmitNews($obj, $content);
// 			break;

// 		case INPUT_TOOLS_JOKE:
// 			$url = "http://apix.sinaapp.com/joke/?appkey=trialuser";
// 			$output = file_get_contents($url);
// 			$content = json_decode($output, true);
// 			$result = display_text($obj,  substr($content, 0, strlen($content)-30));
// 			break;

// 		case INPUT_TOOLS_MUSIC:
// 			$music = get_input_real_content($text);
// 			nv_log(__FILE__, __FUNCTION__, "music = $music");
// 			$music_info = get_music_info($music);
// 			nv_log(__FILE__, __FUNCTION__, "music_info = $music_info");
// 			if (is_array($music_info)) {
// 				$result = display_music($obj, $music_info);
// 			} else {
// 				$result = display_text($obj, $music_info);
// 			}
// 			break;

// 		case INPUT_ELSE:
// 		default:
// 			$content = HELLO_MSG;
// 			$result = display_text($obj, $content);
// 			break;
// 	}
// 	return $result;
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