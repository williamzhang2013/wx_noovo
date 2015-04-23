<?php

// costant
// find face constant
define("API_KEY", "443eeb6b92c9c5e33389686939a7583c");
define("API_SECRET", "liv14AYyFJ2OKThNknkpTnpbro2lcEUF");
define("API_URL", "apius.faceplusplus.com");

define("LOG_FILE", "/tmp/log/noovo.log");
define("NV_FILE", "/tmp/log/nv.dat");

define("STATE_IDLE",  0);
define("STATE_ORDER", 1);
define("STATE_LOGIN", 2);
define("STATE_ADMIN", 3);

define("EVENT_UNDEF",         0);
define("EVENT_HELP",          1);
define("EVENT_ABOUT",         2);
define("EVENT_ORDER",         3);
define("EVENT_CONTACT",       4);
define("EVENT_WEATHER",       5);
define("EVENT_JOKE",          6);
define("EVENT_MUSIC",         7);

define("EVENT_QUIT",          8);
define("EVENT_LOGIN",         9);
define("EVENT_LOCK",          10);
define("EVENT_UNLOCK",        11);
define("EVENT_DEL",           12);
define("EVENT_QUERY",         13);
define("EVENT_LSMENU",        14);
define("EVENT_RSV",           15);
define("EVENT_NEXT",          16);
define("EVENT_LIST",          17);

define("ADMIN_NAME",          "nvadmin");
define("ADMIN_PWD",           "platypus");
define("ADMIN_LS_UNDEF",      0);
define("ADMIN_LS_TODAY",      1);
define("ADMIN_LS_DATE",       2);
define("ADMIN_LS_MONTH",      3);
define("ADMIN_LS_USR",        4);

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
define("NAME_FRYING",         "炒菜");
define("NAME_RICE",           "盖饭");
define("NAME_SOUP",           "汤");
define("NAME_SNACK",          "点心");
define("NAME_ATEA",           "下午茶");
define("NAME_MARMITE",        "砂锅");

define("BREAKFAST",           1);
define("LUNCH",               2);
define("SUPPER",              3);
define("TEA",                 4);
define("NSNACK",              5);

define("USER_INFO_VER",       "1.0.0");

define("NOOVO_RECMD", "Noovo推荐");
define("HELLO_ADMIN", "Admin Login!");
define("HELLO_ABOUT", "输入'about'可以浏览公司介绍");
define("HELLO_ORDER", "输入'吃饭'可以订餐");
define("HELLO_CONTACT", "输入'contact+英文名'可以查询员工信息");
define("HELLO_WEATHER", "输入'天气+城市'可以查询城市天气预报");
define("HELLO_JOKE", "输入'joke'可以放松一下");
define("HELLO_MUSIC", "输入'音乐+歌名@歌手'可以查询音乐");
define("HELLO_MSG", "输入'about'可以浏览公司介绍\n输入'吃饭'可以订餐\n输入'contact+英文名'可以查询员工信息\n输入'天气+城市'可以查询城市天气预报\n输入'joke'可以放松一下");

define("PLEASE_LOGIN", "您还没有注册，请注册:\n格式:login+员工名\n例如:login+admin.nv");
define("HELP_NOT_NVNESE", "您不是Noovo电子的员工，请重新注册!");
define("HELP_MENU_FRY",   "输入'menu+炒菜'会显示炒菜菜单\n");
define("HELP_MENU_RICE",  "输入'menu+盖饭'会显示盖浇饭菜单\n");
define("HELP_MENU_SOUP",  "输入'menu+汤'会显示汤\n");
define("HELP_MENU_SNACK", "输入'menu+点心'会显示点心\n");
define("HELP_MENU_TEA",   "输入'menu+下午茶'会显示下午茶内容\n");
define("HELP_ORDER_QUIT", "输入'quit'或'exit'会退出订餐");
define("HELP_ORDER_ORDER", "输入'rsv+菜名@菜名'可以订菜");
define("HELP_ORDER_DEL", "输入'del+菜名@菜名'可以退订");
define("HELP_ORDER_DELALL", "输入'del+all'可以退订全部订菜");
define("HELP_ORDER_LIST", "输入'ls'可以列出当天所订菜谱");
define("AFTER_ORDER_QUIT",  "欢迎使用Noovo订餐系统!\n");

define("HELP_ADMIN_LOCK", "输入'lock'可以锁住订餐系统");
define("HELP_ADMIN_UNLOCK", "输入'unlock'可以解锁订餐系统");
define("HELP_ADMIN_QUERY_TODAY", "输入'ls'可以查询当天的订餐情况");
define("HELP_ADMIN_QUERY_DATE", "输入'ls+date@date(yyyy-mm-dd)'可以查询某一天的订餐情况");
define("HELP_ADMIN_QUERY_WEEK", "输入'ls+week'可以查询本周的订餐情况");
define("HELP_ADMIN_QUERY_MONTH","输入'ls+month@month(yyyy-mm)'可以查询本月的订餐情况");
define("HELP_ADMIN_QUERY_USR", "输入'ls+usr@name@yyyy-mm'可以查询某个用户的订餐情况");
define("HELP_ADMIN_DEL", "输入'del+usr@菜名'可以删除某个用户的某道菜");

define("TODAY_NO_ORDER","今天没人订餐!");
define("TODAY_USR_NOORDER", "今天您没有订餐");
define("TODAY_USR_ORDER", "今天您订了:");
define("ORDER_SYS_LOCKED", "订餐系统已上锁!");
define("ORDER_SYS_UNLOCKED", "订餐系统已解锁!");
define("NO_USR_FOUND", "找不到用户");

define("LINES_PER_PAGE",       100);

// variable
$g_main_state = 0;
$g_sub_state = 0;
$nv_arr = array("g_main_state", "g_sub_state");
?>