<?php

// costant
define("LOG_FILE", "/tmp/log/noovo.log");
define("NV_FILE", "/tmp/log/nv.dat");

define("STATE_INIT",  0);
define("STATE_ORDER", 1);
define("STATE_CONT",  2);

define("EVENT_UNDEF",         "handler_help");
define("EVENT_HELP",          "handler_help");
define("EVENT_ABOUT",         "handler_about");
define("EVENT_ORDER",         "handler_order");
define("EVENT_CONTACT",       "handler_contact");
define("EVENT_WEATHER",       "handler_weather");
define("EVENT_JOKE",          "handler_joke");
define("EVENT_MUSIC",         "handler_music");

// define("INPUT_ELSE",          0);
// define("INPUT_ABOUT",         1);
// define("INPUT_LUNCH",         2);
// define("INPUT_CONTACT",       3);
// define("INPUT_TOOLS",         4);
// define("INPUT_TOOLS_WEATHER", 41);
// define("INPUT_TOOLS_JOKE",    42);
// define("INPUT_TOOLS_MUSIC",   43);

define("HELLO_ABOUT",         "输入'about'可以浏览公司介绍\n");
define("HELLO_ORDER",         "输入'吃饭'可以订餐\n");
define("HELLO_CONTACT",       "输入'contact+英文名'可以查询员工信息\n");
define("HELLO_WEATHER",       "输入'天气+城市'可以查询城市天气预报\n");
define("HELLO_JOKE",          "输入'joke'可以放松一下");
define("HELLO_MUSIC",         "输入'music+歌名@歌手'可以点歌");
define("HELLO_MSG", "输入'about'可以浏览公司介绍\n输入'吃饭'可以订餐\n输入'contact+英文名'可以查询员工信息\n输入'天气+城市'可以查询城市天气预报\n输入'joke'可以放松一下");

// variable
$g_main_state = 0;
$g_sub_state = 0;
$nv_arr = array("g_main_state", "g_sub_state");
?>