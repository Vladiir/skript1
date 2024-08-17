<?php

// очищаем ккеш -------
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
//---------------------
ini_set('max_execution_time', '1700');
set_time_limit(1700);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
//header('Access-Control-Allow-Headers: Content-Type');
//header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

include ('config.php');
http_response_code(200);
/*
// proverka HEADERS -----------
$headers = getallheaders();

	if($headers['X-Bearer-Token'] === $server_token_true){
		http_response_code(200);
	}else{
	 	http_response_code(403);
	 	exit;
	}
// ----------------------------
*/

/*
$inputJSON = '
{
"action":"new_user",
"userid":"1112",
"priglasitelid":"22",
"tgid":"999",
"username":"zzz",
"username0":"zzz0",
"fullName":"xxx",
"firstName":"ccc",
"fio":"Яяяяя",
"phone":"+380671112233",
"lastName":""
}
';
*/
/*
$inputJSON = '
{
"action":"statistic_user",
"userid":"22"
}
';
*/
/*
$inputJSON = '
{
"action":"base_user",
"userid":"22"
}
';
*/
/*
$inputJSON = '
{
"action":"sponsor_user",
"userid":"1112"
}
';
*/
/*
$inputJSON = '
{
"action":"update_user_fio",
"userid":"22",
"fio":"22zz"
}
';
*/
/*
$inputJSON = '
{
"action":"update_user_username0",
"userid":"22",
"username0":"uszz"
}
';
*/
/*
$inputJSON = '
{
"action":"update_user_phone",
"userid":"22",
"phone":"+380998885522"
}
';
*/
/*
$inputJSON = '
{
"action":"update_user_ref_link",
"userid":"22",
"ref_link":"https://www.piliapp.com/"
}
';
*/
/*
$inputJSON = '
{
"action":"is_sponsor",
"userid":"31",
"priglasitelid":"226"
}
';
*/
/*
$inputJSON = '
{
"action":"change_status",
"userid":"31",
"status":"active"
}
';
*/
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, TRUE); //convert JSON into array

if(!$input['action']) exit;

include ('functions.php');

logfile_new($inputJSON, 'log.txt');

$action = $input['action'];
$userid = $input['userid'];
$tgid = $input['tgid'];
$status = $input['status'];
$username = $input['username'];
$username0 = $input['username0'];
$fio = $input['fio'];
$phone = $input['phone'];
$fullName = $input['fullName'];
$firstName = $input['firstName'];
$ref_link = $input['ref_link'];
$priglasitelid = $input['priglasitelid'];

//================================

if($action === 'new_user' && $userid){

$params = [
    'userid' => $userid
];
$sql = "SELECT id, status FROM ".$pref."_users WHERE userid = :userid LIMIT 1";
$result = sqlnew($sql, $config, $params);
//var_dump($result);

// такой пользователь уже есть в базе
if($result[0]['id']){
	$jsonAnswer['status'] = 'error';
	$jsonAnswer['is_user'] = 'yes';
	logfile_new($jsonAnswer, 'log.txt');
	echo json_encode($jsonAnswer);
	exit;	
}

//---------------	
	
//var_dump($input);
	$sponsorNid = $priglasitelid;	

//---------------	
function insert_new($userid, $tgid, $username, $username0, $fio, $phone, $fullName, $firstName, $lastName, $sponsorNid, $sponsorNlink, $priglasitelid, $sp_add, $pref, $config){
$params = [
    'userid' => $userid,
    'tgid' => $tgid,
    'username' => $username,
	'username0' => $username0,
	'fio' => $fio,
   'phone' => $phone,
	'fullName' => $fullName,
	'firstName' => $firstName,
	'lastName' => $lastName,
	'priglasitelid' => $priglasitelid,
	'spr1' => $sponsorNid
];
	
if($sponsorNid !== $priglasitelid){
	$pereliv = true;
	$pereliv_txt = "pereliv = 1 ,";
}
	
$sql = "INSERT IGNORE INTO ".$pref."_users SET userid = :userid, tgid = :tgid, username = :username, username0 = :username0, fio = :fio, phone = :phone, fullName = :fullName, firstName = :firstName, lastName = :lastName, priglasitelid = :priglasitelid, status = 'passive', $pereliv_txt spr1 = :spr1 $sp_add";
	
//$sql = "INSERT IGNORE INTO ".$pref."_users SET userid = :userid, tgid = :tgid, username = :username, username0 = :username0, fio = :fio, fullName = :fullName, firstName = :firstName, lastName = :lastName, priglasitelid = $priglasitelid, status = 'passive', spr1 = $sponsorNid $sp_add";	
//var_dump($sql);		
$result = sqlnew($sql, $config, $params);
var_dump($result);	
	
	if(!$pereliv){
		$text = "✅ В первой лини у вас новый кандидат: \n🧑‍💻 Имя: $fio \n😀 Ник: $username0";
	}else{
		$text = "✅ В первой лини у вас новый кандидат: \n🧑‍💻 Имя: $fio \n😀 Ник: $username0 \n(Перелив)";	
	}
	
	$itog = ma_sendContent($sponsorNid, $sponsorNlink, $text, $config['ma_token']);
	
	//var_dump($itog);
	$jsonAnswer['status'] = 'ok';
	$jsonAnswer['in_base'] = 1;
	$jsonAnswer['sponsor_link'] = $sponsorNlink;
	
	
	echo json_encode($jsonAnswer);
	
	
	exit; //оставиь !!!
}
	
//---------------
	
	
function sponsorNdata($sponsorNid, $pref, $config){
//получем данные спонсора --------------	$priglasitel
$params = [
    'sponsorNid' => $sponsorNid
];

$sql = "SELECT * FROM ".$pref."_users WHERE userid = :sponsorNid LIMIT 1";
//var_dump($sql);	
$sponsorN_0 = sqlnew($sql, $config, $params);
$sponsorN = $sponsorN_0[0];	
//var_dump($sponsorN);	

// такого пригласителя нет в базе
if(!$sponsorN['id']){
	$jsonAnswer['status'] = 'error';
	$jsonAnswer['is_sponsor'] = 'no';
	logfile_new($jsonAnswer, 'log.txt');
	echo json_encode($jsonAnswer);
	exit;
}	
	return $sponsorN;
}
	
//---------------
	
function pereliv($userid, $tgid, $username, $username0, $fio, $phone, $fullName, $firstName, $lastName, $sponsorNid, $sponsorNlink, $priglasitelid, $lineSpr, $pref, $config){	
	
$sponsorN = sponsorNdata($sponsorNid, $pref, $config);
$sponsorNlink = $sponsorN['ref_link'];

//добавление всей цепочки spr вверх
foreach($sponsorN as $k => $v){
		
		//prefix v tablice	
		$sufix = 'spr';
				
		if(mb_strpos($k, $sufix) === 0) {
			$level_sponsor1 = substr($k, 3);
			if($v) $sponsors_id[$level_sponsor1] = $v;
	}
}

if(is_array($sponsors_id)){
	$sp_add = '';
	foreach($sponsors_id as $k2 => $v2){
		$kk = $k2+1;
		$sp_add = $sp_add.", spr".$kk." = '".$v2."'";
	}
}	
//var_dump($sp_add);	
	//echo"1119\n";
//------------------------------------	


if($sponsorN['status'] === 'active'){

	
//status активный --------
//echo"111\n";	
$params = [
    'sponsorNid' => $sponsorNid
];

//поиск активных в первой линии	
$sql = "SELECT userid FROM ".$pref."_users WHERE spr".$lineSpr." = :sponsorNid AND status = 'active'";
$active1 = sqlnew($sql, $config, $params);
	//echo"112\n";
//var_dump($active1);
$count_active = count($active1);
//var_dump($count_active);	



//эсли это сам пригласитель -----------
if($sponsorNid == $priglasitelid) {
//поиск количества регистраций по реф ссылке прглсителя
$sql = "SELECT userid FROM ".$pref."_users WHERE priglasitelid = :sponsorNid";
$result = sqlnew($sql, $config, $params);
$priglasitel_invite = count($result);
$priglasitel_invite++;
//var_dump($priglasitel_invite);
	//var_dump($params);
	if($count_active >=3 && isMultiple($priglasitel_invite, 6)){
		$is_priglasitelid = true;
	}	
}
//-------------------------------------



	
	//exit;
if($count_active < 2 || $is_priglasitelid){
//записываем в первую линию---	

//var_dump($sp_add);
insert_new($userid, $tgid, $username, $username0, $fio, $phone, $fullName, $firstName, $lastName, $sponsorNid, $sponsorNlink, $priglasitelid, $sp_add, $pref, $config);
	
}else{
//перелив ---

if($count_active > 0){	
	
foreach($active1 as $v){
	$active_first_line[] = $v['userid'];
}
	//var_dump($active_first_line);
	
// Получаем случайный ключ
$randomKey = array_rand($active_first_line);

// Получаем случайное значение
$random_partner = $active_first_line[$randomKey];	
//var_dump($random_partner);	
//echo"1117\n";	
	$sponsorNid = $random_partner;
	pereliv($userid, $tgid, $username, $username0, $fio, $phone, $fullName, $firstName, $lastName, $sponsorNid, $sponsorNlink, $priglasitelid, $lineSpr, $pref, $config);
}else{
	//1111111111111111111111111111111111
	echo"============================================<br>";
}
	

}


	
//-----------------------------	




}else{
//status НЕ активный --------
	//echo"317\n";
	//var_dump($sponsorN['userid']);
	//var_dump($sponsorN['status']);
//exit;	
$params = [
    'sponsorNid1' => $sponsorNid
];
	
//$config['max_base_lelels']=2;
$sp_add2 = "spr1 = :sponsorNid1";
for ($i = 2; $i <= $config['max_base_lelels']; $i++) {
    $sp_add2 = $sp_add2." OR spr".$i." = :sponsorNid".$i;
	$sponsorNid_num = 'sponsorNid'.$i;
	$params[$sponsorNid_num] = $sponsorNid;
}
//var_dump($params);	

//поиск есть ли вообщелюди в его ветке	
$sql = "SELECT userid, status FROM ".$pref."_users WHERE $sp_add2";

//var_dump($config['max_base_lelels']);	
$vetka_all = sqlnew($sql, $config, $params);
//var_dump($vetka_all);
$count_vetka_all = count($vetka_all);
//var_dump($count_vetka_all);	
	

	
//если никого нет в ветке, распределяем ему в первую линию
if($count_vetka_all == 0){
// INSERT $sponsorNid	555
	insert_new($userid, $tgid, $username, $username0, $fio, $phone, $fullName, $firstName, $lastName, $sponsorNid, $sponsorNlink, $priglasitelid, $sp_add, $pref, $config);

	//echo"126\n";
}else{
	//echo"127\n";
foreach($vetka_all as $v){
	if($v['status'] == 'active') $vetka_all_active[] = $v['userid'];
}
//var_dump($vetka_all_active);		
$count_vetka_all_active = count($vetka_all_active);	
	
//если нет активных в ветке, распределяем рандомно любому в ветке
if($count_vetka_all_active == 0){
$randomKey = array_rand($vetka_all);
$sponsorNid = $vetka_all[$randomKey]['userid'];		
// INSERT $sponsorNid	555
//var_dump($sponsorNid);	
	//var_dump($sp_add);	
	//echo"128\n";
insert_new($userid, $tgid, $username, $username0, $fio, $phone, $fullName, $firstName, $lastName, $sponsorNid, $sponsorNlink, $priglasitelid, $sp_add, $pref, $config);


	
}else{
	//echo"129\n";
//если есть активные в ветке, распределяем рандомно любому активному в ветке по схеме	
	
$params = [
    'sponsorNid' => $sponsorNid
];
//333333333333333333333333333333333333333333333333333333333333
//поиск активных в первой линии	
for ($i = 1; $i <= $config['max_base_lelels']; $i++) {	
$sql = "SELECT userid FROM ".$pref."_users WHERE spr".$i." = :sponsorNid AND status = 'active'";
	//var_dump($sql);
$vetka_all_activeN = sqlnew($sql, $config, $params);
//var_dump($vetka_all_activeN);
$count_vetka_all_activeN = count($vetka_all_activeN);
//var_dump($count_vetka_all_activeN);	

if($count_vetka_all_activeN > 0){
	
$randomKey = array_rand($vetka_all_activeN);
$sponsorNid = $vetka_all_activeN[$randomKey]['userid'];	
	//var_dump($count_vetka_all_activeN);	
	//var_dump($sponsorNid);	
	break;
}
}	

	
pereliv($userid, $tgid, $username, $username0, $fio, $phone, $fullName, $firstName, $lastName, $sponsorNid, $sponsorNlink, $priglasitelid, $lineSpr, $pref, $config);
//echo"130\n";
	

}
	
	
	
	
}
	
	
//exit;		
	
	
/*
$params = [
    'sponsorNid' => $sponsorNid
];
$sql = "SELECT userid FROM ".$pref."_users WHERE status = 'active'";
$active_all_base = sqlnew($sql, $config, $params);
var_dump($sql);	
}
*/	
	
	
	
}



// END FUNC	
}	

	
	
	
	
	
	
	
$lineSpr = 1;	

	pereliv($userid, $tgid, $username, $username0, $fio, $phone, $fullName, $firstName, $lastName, $sponsorNid, $sponsorNlink, $priglasitelid, $lineSpr, $pref, $config);
	
	


	
	
exit;
}

//================================

if($action === 'statistic_user' && $userid){

$sp_add2 = "spr1 = $userid";
for ($i = 2; $i <= $config['max_base_lelels']; $i++) {
    $sp_add2 = $sp_add2." OR spr".$i." = $userid";
}
//var_dump($sp_add2);	

	
	
	

//$sql = "SELECT * FROM ".$pref."_users WHERE $sp_add2";
//------	
$sql = "SELECT id FROM ".$pref."_users WHERE priglasitelid = $userid";
$result = sqlnew($sql, $config);
//var_dump($result);
$count_perehod_po_ssylke = count($result);
//var_dump($count_perehod_po_ssylke);	
//------	
$sql = "SELECT userid, status, username0, fio, pereliv, priglasitelid, spr1 FROM ".$pref."_users WHERE $sp_add2";
//var_dump($sql);
$all_vetka = sqlnew($sql, $config);
//var_dump($all_vetka);
$count_vsia_vetka = count($all_vetka);
//var_dump($count_perehod_po_ssylke);	
//------
	$count_perelivy_ot_menia = 0;
	$count_perelivy_ot_verhnih = 0;
	$count_pervaya_liniya = 0;
	$count_pervaya_liniya_active = 0;
	$count_all_active = 0;
    foreach ($all_vetka as $v) {
        if (isset($v['pereliv']) && $v['pereliv'] == 1) {
            if ($userid == $v['priglasitelid']) {
                $count_perelivy_ot_menia++;
            } else {
                $count_perelivy_ot_verhnih++;
            }
        }
	
		if ($userid == $v['spr1']) {
			$count_pervaya_liniya++;
			$pervaya_liniya[] = $v;
			
			if ($v['status'] == 'active') {
				$count_pervaya_liniya_active++;
			}
		}
		
		if ($v['status'] == 'active') {
			$count_all_active++;
		}
		
		
    }
	
	$count_pervaya_liniya_NOTactive = $count_pervaya_liniya - $count_pervaya_liniya_active;
	$count_all_NOTactive = $count_vsia_vetka - $count_all_active;
	
  // var_dump($count_vsia_vetka);
	//var_dump($count_perelivy_ot_menia);
	//var_dump($count_perelivy_ot_verhnih);
	//var_dump($count_pervaya_liniya);
	//	var_dump($count_pervaya_liniya_active);
	//var_dump($count_pervaya_liniya_NOTactive);
	//var_dump($count_all_active);

$text = "▪️ Переходы по ссылке: $count_perehod_po_ssylke \n▪️ Переливы партнерам: $count_perelivy_ot_menia \n▪️ Переливы от вышестоящих партнеров: $count_perelivy_ot_verhnih \n▪️ Кандидатов в 1 линии: $count_pervaya_liniya_NOTactive \n▪️ Активных партнеров 1 линии: $count_pervaya_liniya_active \n▪️ Кандидатов в структуре: $count_all_NOTactive \n▪️ Активных партнеров в структуре: $count_all_active\n\n/cabinet - личный кабинет";
	
//$userid = 886984781;
	$itog = ma_sendContent($userid, $text, $config['ma_token']);
	
	var_dump($itog);
	
	
	exit; //оставиь !!!

/*
 Партнеры могут видеть статистику:



Данные вышестоящего партнера (Имя, номер, и телеграм @username)
Если возможно Базу всех кандидатов и партнеров (до 10 линии)

*/
	
exit;
}
//================================


//================================

if($action === 'base_user' && $userid){

$sp_add2 = "spr1 = $userid";
for ($i = 2; $i <= $config['max_base_lelels']; $i++) {
    $sp_add2 = $sp_add2." OR spr".$i." = $userid";
}
//var_dump($sp_add2);	


//------	
$sql = "SELECT userid, status, username0, fio, pereliv, priglasitelid, spr1 FROM ".$pref."_users WHERE $sp_add2";
//var_dump($sql);
$all_vetka = sqlnew($sql, $config);
//var_dump($all_vetka);
$count_vsia_vetka = count($all_vetka);
//var_dump($count_perehod_po_ssylke);	
//------

if($count_vsia_vetka > 0){
	foreach($all_vetka as $v){
		if($v['status'] == 'active'){
			$text1 = $text1."▪️ ".$v['fio']." (".$v['username0'].")\n";
		}else{
			$text2 = $text2."▪️ ".$v['fio']." (".$v['username0'].")\n";
		}	
	}
	
	$text = "Активные партнеры\n".$text1."\n"."Кандидаты\n".$text2."\n\n/cabinet - личный кабинет";
	
}else{
$text = "У вас нет партнеров в структуре\n\n/cabinet - личный кабинет";	
}

//$userid = 886984781;
	$itog = ma_sendContent($userid, $text, $config['ma_token']);
	
	var_dump($itog);
	
	
	exit; //оставиь !!!

}

//================================

if($action === 'sponsor_user' && $userid){

//------	
$sql = "SELECT spr1 FROM ".$pref."_users WHERE userid = $userid LIMIT 1 ";
//var_dump($sql);
$user = sqlnew($sql, $config);
//var_dump($user);
	
	$sponsorid = $user[0]['spr1'];

$sql = "SELECT * FROM ".$pref."_users WHERE userid = $sponsorid LIMIT 1 ";
//var_dump($sql);
$sponsor = sqlnew($sql, $config);
//var_dump($sponsor);	

	$text = "Ваш спонсор\n▪️ Имя: ".$sponsor[0]['fio']."\n▪️ Ник: ".$sponsor[0]['username0']."\n▪️ Телефон: ".$sponsor[0]['phone']."\n▪️ userid: ".$sponsor[0]['userid']."\n\n/cabinet - личный кабинет";
	
//$userid = 886984781;
	$itog = ma_sendContent($userid, $text, $config['ma_token']);
	
	var_dump($itog);
	
	
	exit; //оставиь !!!

}
//================================

if($action === 'update_user_fio' && $userid && $fio){

	
$params = [
	'userid' => $userid,
	'fio' => $fio
];
	

$sql = "UPDATE ".$pref."_users SET fio = :fio WHERE userid = :userid LIMIT 1";
//var_dump($sql);		
$result = sqlnew($sql, $config, $params);
//var_dump($result);	
	


$text = "Ваше имя обновлено\n\n/cabinet - личный кабинет";	
	
//$userid = 886984781;
	$itog = ma_sendContent($userid, $text, $config['ma_token']);
	var_dump($itog);
	exit; //оставиь !!!

}

//================================

if($action === 'update_user_username0' && $userid && $username0){
	
$params = [
	'userid' => $userid,
	'username0' => $username0
];

$sql = "UPDATE ".$pref."_users SET username0 = :username0 WHERE userid = :userid LIMIT 1";
//var_dump($sql);		
$result = sqlnew($sql, $config, $params);
//var_dump($result);	

$text = "Ваш ник обновлен\n\n/cabinet - личный кабинет";	
	
//$userid = 886984781;
	$itog = ma_sendContent($userid, $text, $config['ma_token']);
	var_dump($itog);
	exit; //оставиь !!!

}

//================================

if($action === 'update_user_phone' && $userid && $phone){
	
$params = [
	'userid' => $userid,
	'phone' => $phone
];

$sql = "UPDATE ".$pref."_users SET phone = :phone WHERE userid = :userid LIMIT 1";
//var_dump($sql);		
$result = sqlnew($sql, $config, $params);
//var_dump($result);	

$text = "Ваш телефон обновлен\n\n/cabinet - личный кабинет";	
	
//$userid = 886984781;
	$itog = ma_sendContent($userid, $text, $config['ma_token']);
	var_dump($itog);
	exit; //оставиь !!!

}

//================================

if($action === 'update_user_ref_link' && $userid && $ref_link){
	
$params = [
	'userid' => $userid,
	'ref_link' => $ref_link
];

$sql = "UPDATE ".$pref."_users SET ref_link = :ref_link WHERE userid = :userid LIMIT 1";
//var_dump($sql);		
$result = sqlnew($sql, $config, $params);
//var_dump($result);	

$text = "Ваша ссылка обновлена\n\n/cabinet - личный кабинет";	
	
//$userid = 886984781;
	$itog = ma_sendContent($userid, $text, $config['ma_token']);
	var_dump($itog);
	exit; //оставиь !!!

}

//================================
	
if($action === 'update_user_all' && $userid){
	
$params = [
	'userid' => $userid,
	'fio' => $fio,
	'username0' => $username0,
	'phone' => $phone,
	'ref_link' => $ref_link
];

$sql = "UPDATE ".$pref."_users SET fio = :fio, username0 = :username0, phone = :phone, ref_link = :ref_link WHERE userid = :userid LIMIT 1";
//var_dump($sql);		
$result = sqlnew($sql, $config, $params);
//var_dump($result);	

	$jsonAnswer['status'] = 'ok';
	echo json_encode($jsonAnswer);	
	
	exit; //оставиь !!!

}

//================================

if($action === 'is_sponsor' && $userid && $priglasitelid){
	
$params = [
	'priglasitelid' => $priglasitelid
];
	
$sql = "SELECT userid FROM ".$pref."_users WHERE userid = :priglasitelid LIMIT 1 ";
//var_dump($sql);
$priglasitel = sqlnew($sql, $config, $params);
//var_dump($priglasitel);
	
	if(count($priglasitel) > 0){
		$jsonAnswer['status'] = 'ok';
		$jsonAnswer['is_sponsor'] = 1;
	}else{
		$jsonAnswer['status'] = 'error';
		$jsonAnswer['is_sponsor'] = 0;
	}
	
	echo json_encode($jsonAnswer);
	exit;

}

//================================

if($action === 'change_status' && $userid && $status){
	
$params = [
	'userid' => $userid,
	'status' => $status
];

$sql = "UPDATE ".$pref."_users SET status = :status WHERE userid = :userid LIMIT 1";
//var_dump($sql);		
$result = sqlnew($sql, $config, $params);
//var_dump($result);	

		$jsonAnswer['status'] = 'ok';
	echo json_encode($jsonAnswer);

	exit; //оставиь !!!

}

//================================

//update_user_username0



//logfile_new($log, 'log.txt');



// ----------------------------

// ----------------------------
































exit;




?>

