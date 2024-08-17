<?php

date_default_timezone_set('Etc/GMT-3'); // utc php

$config['max_base_lelels'] = 50;  // максималный размер базы в глубину

$config['server'] = "localhost";  // host
$config['database'] = "tsyndren_systemtop";  // host
$config['username'] = "tsyndren_Zavod";  // host
$config['password'] = "Zavod050981";  // host
$pref = 'mlm'; // префикс таблиц
$config['utc'] = "+3"; // utc базы
$config['num_pereliv'] = 2;  // перелив, если у пригласителя есть num_pereliv активных партнера в первой линии
$config['num_activ_first'] = 3; // к пригласителю в первую линию будет переходить каждый num_personal партнер, если в первой линии есть num_activ_first активных 
$config['num_personal'] = 10;  // к пригласителю в первую линию будет переходить каждый num_personal партнер, если в первой линии есть num_activ_first активных


$config['ma_token'] = "1642420:bdfff6ffdf916c94d65e270ac6509742";  // токен меничата
//$config['bot_token'] = 50;  "7035419361:AAGVUiWYfndFltN6qmZQsErV4d3h4IpeZuA";  // токен бота
//$config['name_main_bot'] = 50;  "System TOP";  // имя бота
//$config['$chat_id'] = 387210863;  // телеграм id админа

$server_token_true = 'mlmbooster_cjcve383fjvi834e38fj'; //

//$id_group_admin = '285419619'; // sergey
//$id_group_admin = '-100171237041';// админ тест


