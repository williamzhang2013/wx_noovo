<?php

// costant
define("LOG_FILE", "/tmp/log/noovo.log");
define("NV_FILE", "/tmp/log/nv.dat");

define("STATE_INIT",  0);
define("STATE_ORDER", 1);
define("STATE_CONT",  2);

define("EVENT_UNDEF",         0);
define("EVENT_HELP",          1);
define("EVENT_ABOUT",         2);
define("EVENT_ORDER",         3);
define("EVENT_CONTACT",       4);
define("EVENT_WEATHER",       5);
define("EVENT_JOKE",          6);
define("EVENT_MUSIC",         7);

define("EVENT_QUIT",          8);

// define("INPUT_ELSE",          0);
// define("INPUT_ABOUT",         1);
// define("INPUT_LUNCH",         2);
// define("INPUT_CONTACT",       3);
// define("INPUT_TOOLS",         4);
// define("INPUT_TOOLS_WEATHER", 41);
// define("INPUT_TOOLS_JOKE",    42);
// define("INPUT_TOOLS_MUSIC",   43);

define("FRYING",              1);
define("RICE",                2);
define("SOUP",                3);
define("SNACK",               4);
define("ATEA",                5);
define("MARMITE",             6);

define("BREAKFAST",           1);
define("LUNCH",               2);
define("SUPPER",              3);
define("TEA",                 4);
define("NSNACK",              5);

define("USER_INFO_VER",       "1.0.0");

define("HELLO_ABOUT", "输入'about'可以浏览公司介绍");
define("HELLO_ORDER", "输入'吃饭'可以订餐");
define("HELLO_CONTACT", "输入'contact+英文名'可以查询员工信息");
define("HELLO_WEATHER", "输入'天气+城市'可以查询城市天气预报");
define("HELLO_JOKE", "输入'joke'可以放松一下");
define("HELLO_MUSIC", "输入'音乐+歌名@歌手'可以查询音乐");
define("HELLO_MSG", "输入'about'可以浏览公司介绍\n输入'吃饭'可以订餐\n输入'contact+英文名'可以查询员工信息\n输入'天气+城市'可以查询城市天气预报\n输入'joke'可以放松一下");

define("PLEASE_LOGIN", "您还没有注册，请输入您的英文名:\n");
define("HELP_MENU_FRY",   "输入'menu+炒菜'会显示炒菜菜单\n");
define("HELP_MENU_RICE",  "输入'menu+盖饭'会显示盖浇饭菜单\n");
define("HELP_MENU_SOUP",  "输入'menu+汤'会显示汤\n");
define("HELP_MENU_SNACK", "输入'menu+点心'会显示点心\n");
define("HELP_MENU_TEA",   "输入'menu+下午茶'会显示下午茶内容\n");
define("HELP_ORDER_QUIT", "输入'quit'或'exit'会退出订餐");

define("AFTER_ORDER_QUIT",  "欢迎使用Noovo订餐系统!\n");

// variable
$g_main_state = 0;
$g_sub_state = 0;
$nv_arr = array("g_main_state", "g_sub_state");
?>