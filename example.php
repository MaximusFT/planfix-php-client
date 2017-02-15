<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
//error_reporting(E_STRICT);
/*
require 'Planfix_API.php';
$PF = new Planfix_API(array('apiKey' => '90f49c97f5fcfa8b6d439e40edb43145', 'apiSecret' => '059824052f13a2b722aa400bfca72329'));
$PF->setAccount('tt');
session_start();
if (empty($_SESSION['planfixSid'])) {
    $PF->setUser(array('login' => 'Bardeniuk', 'password' => 'defka12345'));
    $PF->authenticate();
    $_SESSION['planfixSid'] = $PF->getSid();
}
$PF->setSid($_SESSION['planfixSid']);
$method = 'task.getList';
$params = array(
    'user' => array(
        array('id' => 1)
    ),
    'pageCurrent' => 1
);
$clients = $PF->api($method, $params);
echo '<pre>'.print_r($clients, 1).'</pre>';
$batch = array(
    array(
        'method' => 'project.getList',
        'params' => array(
            'user' => array(
                array('id' => 1)
            ),
            'pageCurrent' => 1
        )
    ),
    array(
        'method' => 'user.getList',
        'params' => array(
            'user' => array(
                array('id' => 1)
            ),
            'pageCurrent'   => 1
        )
    )
);
list($projects, $tasks) = $PF->api($batch);
echo '<pre>'.print_r($tasks, 1).'</pre>';
echo '<pre>'.print_r($projects, 1).'</pre>';
*/



/*

$api_server = 'https://api.planfix.ru/xml/';
$api_key = '90f49c97f5fcfa8b6d439e40edb43145';// смотри http://dev.planfix.ru/
$api_secret = '059824052f13a2b722aa400bfca72329';
$planfixAccount = 'tt';
$planfixUser = 'Bardeniuk';
$planfixUserPassword = 'defka12345';

include 'lib.php';

$requestXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><request method="auth.login"><account></account><login></login><password></password></request>');

$requestXml->account = $planfixAccount;
$requestXml->login =  $planfixUser;
$requestXml->password = $planfixUserPassword;
$requestXml->signature = make_sign($requestXml, $api_secret);

$result = apiRequest($api_server, $api_key, $requestXml);
if(!$result['success']) {
	echo $result['response'];
	exit();
}
$apiResult = $result['response'];
parseAPIError($apiResult);
// Важно понимать:
//1 - что полученный идентификатор сессии необходим для вызова всех остальных функций;
// 2 - время жизни сессии ограничено 20-ю минутами;
// 3 - при каждом следующем вызове это время продлевается;
// 4 - сессию не надо получать перед каждым вызовом функции (количество запросов ограничено);
$api_sid = $apiResult->sid;
echo "sid is: $api_sid<br>";
// получаем список доступных нам проектов и выводим его    
// используем функции на: http://goo.gl/E41Vv
$requestXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><request method="task.getList"><account></account><sid></sid><target>out</target><target>in</target></request>');

$requestXml->account = $planfixAccount;
$requestXml->sid = $api_sid;
$requestXml->pageCurrent = 1;
// остальные параметры являются необязательными, поэкспериментируйте сами
$requestXml->signature = make_sign($requestXml, $api_secret);
$result = apiRequest($api_server, $api_key, $requestXml);
if(!$result['success']) {
	echo $result['response'];
	exit();
}
$apiResult = $result['response'];

echo '<pre>';
	print_r($requestXml);
echo '</pre>';

parseAPIError($apiResult);
$totalCount = $apiResult->tasks['totalCount'];
$count = $apiResult->tasks['count'];
echo "Всего задач $totalCount<br>";
echo "Получено задач $count<br>";

foreach($apiResult->tasks->task as $tasks) {
	echo "Задача:{$tasks->title} создатель:{$tasks->owner->name}";
	echo '<br>';
}*/


























$api_server = 'https://api.planfix.ru/xml/';
$api_key = '90f49c97f5fcfa8b6d439e40edb43145';// смотри http://dev.planfix.ru/
$api_secret = '059824052f13a2b722aa400bfca72329';
$planfixAccount = 'tt';
$planfixUser = 'Bardeniuk';
$planfixUserPassword = 'defka12345';

include 'lib.php';

if (empty($_SESSION['planfixSid'])) {

	$requestXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><request method="auth.login"><account></account><login></login><password></password></request>');

	$requestXml->account = $planfixAccount;
	$requestXml->login =  $planfixUser;
	$requestXml->password = $planfixUserPassword;
	$requestXml->signature = make_sign($requestXml, $api_secret);

	$result = apiRequest($api_server, $api_key, $requestXml);
	if(!$result['success']) {
		echo $result['response'];
		exit();
	}
	$apiResult = $result['response'];
	parseAPIError($apiResult);
	$_SESSION['planfixSid'] = $apiResult->sid;

}



function ApiTask($method){
	
	 global $apiResult;
	 
		$api_server = 'https://api.planfix.ru/xml/';
		$api_key = '90f49c97f5fcfa8b6d439e40edb43145';// смотри http://dev.planfix.ru/
		$api_secret = '059824052f13a2b722aa400bfca72329';
		$planfixAccount = 'tt';
		$requestXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
												<request method="'.$method.'">
													<account></account>
														<sid></sid>
												</request>');

		$requestXml->account = $planfixAccount;
		$requestXml->sid = $_SESSION['planfixSid'];
		$requestXml->pageCurrent = 1;
		$requestXml->signature = make_sign($requestXml, $api_secret);
		$result = apiRequest($api_server, $api_key, $requestXml);
		if(!$result['success']) {
			echo $result['response'];
			exit();
		}
		
		
		 $apiResult = $result['response'];
		 parseAPIError($apiResult);
}
		
		ApiTask('task.getList');
		
		
		
		echo "Всего задач ".$apiResult->tasks['totalCount']."<br>";
		echo "Получено задач ".$apiResult->tasks['count']."<br>";

		foreach($apiResult->tasks->task as $tasks) {
			echo "Задача:{$tasks->title} создатель:{$tasks->owner->name}";
			echo '<br>';
		}
		
		
		
		

		
		
		
		
		
		
		
		
		
		
		/*
		echo '<pre>';
			print_r($result['response']);
		echo '</pre>';*/