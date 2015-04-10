<?php
/**
  * wechat php test
  */
header("Content-type: text/html; charset=utf-8");
//define your token
define("TOKEN", "NoovoSH");

define('APPID', "wxe6b4fb10ad8a2436");
define('APPSECRET', "1383d90761ac8ff423a904ae7499cb7d");
define('APP_ID', "wx13312d42ce40f570");
define('APP_SECRET', 'eaa3f3d5de3103a251ab5f3e06053658');

//define("LOG_FILE", "/tmp/log/noovo.log");
require_once(dirname(__FILE__) . '/global.php');
require_once(dirname(__FILE__) . '/utils.php');
require_once(dirname(__FILE__) . '/parser.php');
require_once(dirname(__FILE__) . '/common.php');
require_once(dirname(__FILE__) . '/core.php');

function getAccessToken()
{
    $TOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APP_ID."&secret=".APP_SECRET;
    // if (ini_get('allow_url_fopen') == 1 && function_exists('curl_init')){
    //     $json = file_get_contents($TOKEN_URL);
    //     if (empty($json)){
    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    //         curl_setopt ($ch, CURLOPT_URL, $TOKEN_URL);
    //         curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    //         $json = curl_exec($ch);
    //         curl_close($ch);
    //     }
    //     $result = json_decode($json,true);
    //     @$ACC_TOKEN = $result['access_token'];
    //     $MENU_URL="https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$ACC_TOKEN;
    //     $cu = curl_init();
    //     curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, FALSE);
    //     curl_setopt($cu, CURLOPT_URL, $MENU_URL);
    //     curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
    //     $menu_json = curl_exec($cu);
    //     $info = curl_getinfo($cu);
    //     $menu = json_decode($menu_json);
    //     curl_close($cu);
    //     echo $menu_json;
    // }

    //$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $TOKEN_URL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    $jsoninfo = json_decode($output, true);
    $access_token = $jsoninfo["access_token"];
    return $access_token;
}

$access_token = "";


// function createMenu()
// {
//     $access_token = getAccessToken();
//     $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
//     $result = https_request($url, $jsonmenu);
//     //var_dump($result);

// }


function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

?>
