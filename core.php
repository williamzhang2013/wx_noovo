<?php

//define("LOG_FILE", "/tmp/log/noovo.log");
define("HELLO_MSG", "输入'about'可以浏览公司介绍\n输入'吃饭'可以订餐\n输入'contact+英文名'可以查询员工信息\n输入'天气+城市'可以查询城市天气预报\n输入'joke'可以放松一下");

file_put_contents(LOG_FILE, "MAIN ENTRY\n");
$wechatObj = new noovoCallBack();
//createMenu();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
} else {
    $wechatObj->responseMsg();
}

class noovoCallBack
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
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
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
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

    private function transmitNews($object, $newsArray)
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

    private function receiveEvent($object)
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
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function transmitText($obj, $content)
    {
        if (!isset($content) || empty($content)) {
            return "";
        }

        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";

        $resultStr = sprintf($textTpl, $obj->FromUserName, $obj->ToUserName, time(), $content);
        return $resultStr;

    }

    private function receiveText($obj)
    {
        $text = trim($obj->Content);
        file_put_contents(LOG_FILE, "receive text: $obj->Content, text=$text\n", FILE_APPEND);

        //get the input type
        $input_type = parser_user_input($text);
        $result = "";
        file_put_contents(LOG_FILE, "input_type = $input_type\n", FILE_APPEND);
        switch ($input_type) {
            case INPUT_ABOUT:
                $arrItem = array();
                $arrItem['Title'] = "Noovo电子简介";
                $arrItem['Description'] = "Noovo Technology Corporation_是一个国际化的科技企业，主要从事开发、设计、生产和销售数字电视相关硬件、软件和服务。特别在Wi-Fi DTV Tuner产品领域，Noovo是主要的供应商之一。";
                $arrItem['PicUrl'] = "http://www.noovo.co/upload_file/page/150/clone_714.jpg";
                $arrItem['Url'] = "http://www.noovo.co/companyinformationtw";
                $articles = array($arrItem);
                $result = $this->transmitNews($obj, $articles);
                break;

            case INPUT_CONTACT:
                $name = get_input_real_content($text);
                file_put_contents(LOG_FILE, "name = $name\n", FILE_APPEND);
                //$conn = contact_connect();
                $info = contact_query(strtolower(trim($name)));

                $content = generate_contact_card($info);
                $result = displayText($obj, $content);
                break;

            case INPUT_LUNCH:
                $content = "订餐:Comming soon...";
                $result = displayText($obj, $content);
                break;

            case INPUT_TOOLS_WEATHER:
                $city = get_input_real_content($text);
                file_put_contents(LOG_FILE, "city = $city\n", FILE_APPEND);

                $url = "http://apix.sinaapp.com/weather/?appkey=".$object->ToUserName."&city=".urlencode($city); 
                $output = file_get_contents($url);
                $content = json_decode($output, true);
                $result = $this->transmitNews($obj, $content);
                break;

            case INPUT_TOOLS_JOKE:
                $url = "http://apix.sinaapp.com/joke/?appkey=trialuser";
                $output = file_get_contents($url);
                $content = json_decode($output, true);
                $result = displayText($obj,  substr($content, 0, strlen($content)-30));
                break;

            case INPUT_TOOLS_MUSIC:
                $music = get_input_real_content($text);
                file_put_contents(LOG_FILE, "music = $music\n", FILE_APPEND);
                $music_info = get_music_info($music);
                file_put_contents(LOG_FILE, "music_info = $music_info\n", FILE_APPEND);
                if (is_array($music_info)) {
                	$result = display_music($obj, $music_info);
                } else {
                	$result = displayText($obj, $music_info);
                }
                break;

            case INPUT_ELSE:           
            default:
                $content = HELLO_MSG;
                $result = displayText($obj, $content);
                break;
        }       
        return $result;
    }
  		
	private function checkSignature()
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
}
?>