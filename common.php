<?php

// display
function displayText($obj, $content)
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
    //$result = "";
    if ($name == "") {
        $result = "你还没有输入音乐名称呢";
    } else {
        // song @ singer
        $songPos = strpos("$name", "@");
        $songName = $name;
        $singerName = "";
        $url = "";
        if ($songPos == false) {
            // don't find the singer name
            $url = "http://box.zhangmen.baidu.com/x?op=12&count=1&title=".$name."$$";
        } else {
            $songName = substr($name, 0, $songPos);
            $singerName = substr($name, $songPos+1);
            file_put_contents(LOG_FILE, "songName = $songName, singerName=$singerName\n",FILE_APPEND);
            $url = "http://box.zhangmen.baidu.com/x?op=12&count=1&title=".$songName."$$".$singerName."$$$$";            
        }
        file_put_contents(LOG_FILE, "url=$url\n", FILE_APPEND);
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
            				
            				$result = array("Title"           => $songName,
            							"Description"     =>"Noovo推荐",
            							"MusicUrl"        =>urldecode($music),
            							"HQMusicUrl"      =>urldecode($music),);
            				if ($songPos > 0) {
            					$result["Description"] = $singerName;
            				}
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

// database
function contact_connect()
{
    $conn = mysql_connect("localhost", "root", "root");
    if(!$conn) {
        //die('Could not connect: ' . mysql_error());
        exit(0);
    }
    return $conn;
}

function contact_query($name)
{
    file_put_contents(LOG_FILE, "contact_query!\n", FILE_APPEND);
    $conn = contact_connect(); //mysql_connect("localhost", "root", "root");
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
            file_put_contents(LOG_FILE, "Find the match record! \n", FILE_APPEND);
            return $row;
        }
    }

    // how to return???
    return false;
}

?>