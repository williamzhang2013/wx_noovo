<?php

// costant
define("LOG_FILE", "/tmp/log/noovo.log");
define("NV_FILE", "/tmp/log/nv.dat");

define("STATE_INIT",  0);
define("STATE_ORDER", 1);
define("STATE_CONT",  2);

define("INPUT_ELSE",          0);
define("INPUT_ABOUT",         1);
define("INPUT_LUNCH",         2);
define("INPUT_CONTACT",       3);
define("INPUT_TOOLS",         4);
define("INPUT_TOOLS_WEATHER", 41);
define("INPUT_TOOLS_JOKE",    42);
define("INPUT_TOOLS_MUSIC",   43);


// variable
$g_main_state = 0;
$g_sub_state = 0;
$nv_arr = array("g_main_state", "g_sub_state");
?>