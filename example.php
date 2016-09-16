<?php

require 'Planfix_API.php';

$pf = new Planfix_API(array('apiKey' => 'YOUR_API_KEY', 'apiSecret' => 'YOUR_API_SECRET'));

$pf->setAccount('YOUR_ACCOUNT');

session_start();

if (empty($_SESSION['planfixSid'])) {
    $pf->setUser(array('login' => 'YOUR_LOGIN', 'password' => 'YOUR_PASSWORD'));
    $pf->authenticate();
    $_SESSION['planfixSid'] = $pf->getSid();
}

$pf->setSid($_SESSION['planfixSid']);

$method = 'client.getList';
$params = array(
    'user' => array(
        array('id' => 1)
    ),
    'pageCurrent' => 1
);

$clients = $pf->api($method, $params);

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
        'method' => 'task.getList',
        'params' => array(
            'user' => array(
                array('id' => 1)
            ),
            'pageCurrent'   => 1
        )
    )
);

list($projects, $tasks) = $pf->api($batch);

echo '<pre>'.print_r($tasks, 1).'</pre>';
echo '<pre>'.print_r($projects, 1).'</pre>';