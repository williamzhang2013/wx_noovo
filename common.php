<?php

// display
function display_text($obj, $content)
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

function display_news($object, $newsArray)
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

function display_music($obj, $music)
{
    // if (is_array($music) || count($music) != 0) {
    //     return "";
    // }
    $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[music]]></MsgType>
                <Music>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[%s]]></Description>
                <MusicUrl><![CDATA[%s]]></MusicUrl>
                <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                </Music>
                </xml>";

    $resultStr = sprintf($textTpl, $obj->FromUserName, $obj->ToUserName, time(), 
        $music['Title'], $music['Description'], $music['MusicUrl'], $music['HQMusicUrl']);
    return $resultStr;    
}

function get_music_info($name)
{
    $result = "";
    if ($name == "") {
        $result = "你还没有输入音乐名称呢";
    } else {
        // song @ singer
        $songs = split("@|＠", $name);
        nv_log(__FILE__, __FUNCTION__, "songs[0] = $songs[0], songs[1] = $songs[1]");
        $songName = $songs[0];
        $singerName = $songs[1];
        if (strlen($songs[1]) == 0) {
        	$singerName = NOOVO_RECMD;
        }
        $url = "";
        if (strlen($singerName) == 0) {
            // don't find the singer name
            $url = "http://box.zhangmen.baidu.com/x?op=12&count=1&title=".$songName."$$";
        } else {
            $url = "http://box.zhangmen.baidu.com/x?op=12&count=1&title=".$songName."$$".$singerName."$$$$";            
        }
        nv_log(__FILE__, __FUNCTION__, "url=$url");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);
        $result = "没有找到这首歌曲，换首歌试试吧";
        try{
            $menus = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
            $vvar = var_export($menus, true);
            file_put_contents(LOG_FILE, "var = $vvar\n", FILE_APPEND);
//             $count = $menus->count();
//             if ($count != 0) {
//             	// play the first one
//             	$url = $menus->url;
//             	$encode = $url->encode;
//             	$decode = $url->decode;
            	
//             	$music = substr($encode, 0, strripos($encode, '/')+1).$decode;
//             	file_put_contents(LOG_FILE, "encode=$encode \ndecode=$decode \nmusic=$music\n", FILE_APPEND);
//             	$music_url = urldecode($music);
//             	file_put_contents(LOG_FILE, "music_url=$music_url\n", FILE_APPEND);
//             	//if (!strpos($music, "?") && !strpos($music, "xcode"))
//             	{
//             		file_put_contents(LOG_FILE, "music found is xxx\n", FILE_APPEND);
//             	    $result = array("Title"           => $name,
//             	                    "Description"     =>"WilliamZhang",
//             	                    "MusicUrl"        =>urldecode($music),
//             	                    "HQMusicUrl"      =>urldecode($music),);
//             	    //break;
//             	}
//             }
//             foreach ($menus as $menu) {
//                 file_put_contents(LOG_FILE, "menu = $menu\n", FILE_APPEND);
//                 if (isset($menu->encode) && isset($menu->decode) &&
//                     !strpos($menu->encode, "baidu.com") && strpos($menu->decode, ".mp3")) {
//                     $music = substr($menu->encode, 0, strripos($menu->encode, '/')+1).$menu->decode;
//                     file_put_contents(LOG_FILE, "try to find music... \nmusic=$music\n", FILE_APPEND);
//                     if (!strpos($music, "?") && !strpos($music, "xcode")) {
//                         //file_put_contents(LOG_FILE, "music found is xxx\n", FILE_APPEND);
//                         $result = array("Title"           => $name, 
//                                         "Description"     =>"WilliamZhang",
//                                         "MusicUrl"        =>urldecode($music), 
//                                         "HQMusicUrl"      =>urldecode($music),);
//                         break;
//                     }
//                 }
//             }
            
            foreach ($menus as $menu) {
            	file_put_contents(LOG_FILE, "menu = $menu\n", FILE_APPEND);
            	if (isset($menu->encode) && isset($menu->decode) &&
            			strpos($menu->encode, "baidu.com") && strpos($menu->decode, ".mp3")) {
            				$mp3url = substr($menu->decode, 0, strripos($menu->decode, '&'));
            				$music = substr($menu->encode, 0, strripos($menu->encode, '/')+1).$mp3url;
            				file_put_contents(LOG_FILE, "try to find music... \nmp3url=$mp3url\nmusic=$music\n", FILE_APPEND);
            				
            				$result = array("Title"       => $songName,
            							"Description"     =>$singerName,
            							"MusicUrl"        =>urldecode($music),
            							"HQMusicUrl"      =>urldecode($music),);
//             				if ($songPos > 0) {
//             					$result["Description"] = $singerName;
//             				}
            				break;
            			}
            }
        } catch(Exception $e) {
            //
        }
    }
    return $result;
}

function contact_ename_match($name1, $name2)
{
    // the whole name should be xxx.yyy
    // if xxx == name || xxx.yyy == name || xxxyyy == name means MATCH
    $dotPos = strpos($name2, ".");
    $eName = substr($name2, 0, $dotPos);
    $familyName = substr($name2, $dotPos+1);
    $wholeName = $eName . $familyName;

    if ((strcmp($name1, $eName) == 0)    ||
        (strcmp($name1, $wholeName) == 0) ||
        (strcmp($name1, $name2) == 0) ) {
        return true;
    } else {
        return false;
    }
}

function generate_menu_info()
{
	$menu = array("restName"    =>"饭店名称",
	              "cName"       =>"菜名",
	              "cType"       =>"品种",
	              "cPrice"      =>"价格",
	              "cID"         =>"ID",
	              "cPhoto"      =>"照片",
	              "rsv0"        =>"保留1",
	              "rsv1"        =>"保留2");
}

function generate_order_info() {
	$order = array("openID"     =>"wxID",
	               "eName"      =>"英文名",
	               "date"       =>"日期",
	               "type"       =>"种类",
	               "cID"        =>"ID",
	               "state"      =>"状态");
}

function get_course_type_name($type) {
	$type_names = array(FRYING => NAME_FRYING, RICE    => NAME_RICE,
						SOUP   => NAME_SOUP,   SNACK   => NAME_SNACK,
						ATEA   => NAME_ATEA,   MARMITE => NAME_MARMITE);
	return $type_names[$type];
}

function generate_contact_card($info)
{
    $keywords = array("eCode"     => "工号:",
                      "eName"     => "英文名:",
                      "cName"     => "中文名:",
                      "mail"      => "邮箱:",
                      "mobile1"   => "手机:",
                      "mobile2"   => "手机:",
                      "mobile3"   => "手机:",
                      "mobile4"   => "手机:",
                      "mobile5"   => "手机:",
                      "ext"       => "分机:",);
    $card = "";
    if (is_array($info)) {
        // is the contact array
        foreach ($keywords as $key => $value) {
            if ($info["$key"]) {
                $card .= $value .$info["$key"]."\n";
            }
        }
    }
    return $card;
}

function get_admin_ls_type($lsstr) {
	$type = ADMIN_LS_UNDEF;
	if (strlen($lsstr) == 0) {
		$type = ADMIN_LS_TODAY;
	} else {
		$conditions = split("@|＠", $lsstr);
		if (strcmp($conditions[0], "date") == 0){
			$type = ADMIN_LS_DATE;
		} else if (strcmp($conditions[0], "month") == 0) {
			$type = ADMIN_LS_MONTH;
		} else if (strcmp($conditions[0], "usr") == 0) {
			$type = ADMIN_LS_USR;
		} else {
			$type = ADMIN_LS_UNDEF;
		}
	}
	
	return $type;
}

function is_leap_year($year) {
	$result = 0;
	if ($year%400 == 0 || ($year%4 == 0 && $year%100 != 0)) {
		$result = 1;
	}
	
	return $result;
}

?>